<?php
	if (!file_exists('./includes/mysqlConstants.php'))
	{
		header('location: ./install.php');
		exit();
	}

	require_once('./includes/classFunctions.php');
	session_start();

	$register_terminee = false;
	$i = 0;

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key=>&$val)
	{
		Functions::secure($val);
	}

	// Valeurs par défaut, remplacées si une autre valeur est saisie
	foreach (array('registerUsername', 'registerMail', 'registerPassword1', 'registerPassword2') as $var)
	{
		if (!empty($_[$var]))
		{
			$$var = $_[$var];
		} else {
			$$var = '';
		}
	}


	if (isset($_['registerButton']))
	{
		// Vérification que tous les champs sont renseignés
		if (empty($_['registerUsername']) || empty($_['registerMail']) || empty($_['registerPassword1']) || empty($_['registerPassword2']) || empty($_['registerCaptcha']))
		{
			$registerMessage = 'Veuillez renseigner un nom d\'utilisateur, un email, un mot de passe et le code de sécurité.';
			$i++;
		}

		require_once('./includes/mysqlConstants.php');
		require_once('./includes/mysqlConnector.php');

		$query = $db->prepare('SELECT `username` FROM `site_user` WHERE `username` = :username');
		$query->bindValue(':username', $_['registerUsername'], PDO::PARAM_STR);
		$query->execute();
		$registerVerifUsername = $query->fetch();
		$query->CloseCursor();

		// Vérification si le nom d'utilisateur est présent dans la table site_user
		if ($registerVerifUsername['username'] == $_['registerUsername'] && $i == 0)
		{
			$registerMessageUsername = 'Ce nom d\'utilisateur fait déjà l\'objet d\'un compte enregistré.';
			$i++;
		}

		$query = $db->prepare('SELECT `mail` FROM `site_user` WHERE `mail` = :mail');
		$query->bindValue(':mail', $_['registerMail'], PDO::PARAM_STR);
		$query->execute();
		$registerVerifMail = $query->fetch();
		$query->CloseCursor();

		// Vérification si l'email est présent dans la table site_user
		if ($registerVerifMail['mail'] == $_['registerMail'] && $i == 0)
		{
			$registerMessageMail = 'Cette adresse email fait déjà l\'objet d\'un compte enregistré.';
			$i++;
		}

		// Vérification des 2 mots de passe
		if ($_['registerPassword1'] != $_['registerPassword2'] && $i == 0)
		{
			$registerMessagePassword = 'Les mots de passe ne correspondent pas.';
			$i++;
		}

		// Vérification du captcha
		if ($_['registerCaptcha'] != $_SESSION['aleat_nbr'] && $i == 0)
		{
			$registerMessageCaptcha = 'Le code de sécurité ne correspond pas à l\'image.';
			$i++;
		}
	}

	// Pas d'erreur, l'utilisateur s'inscrit
	if (isset($_['registerButton']) && $i == 0)
	{
		$query = $db->prepare('INSERT INTO `site_user` (`username`, `password`, `mail`, `date_registration`) VALUES (:username, :password, :mail, :date_registration)');
		$query->bindValue(':username', $_['registerUsername'], PDO::PARAM_STR);
		$query->bindValue(':password', md5($_['registerPassword1']), PDO::PARAM_STR);
		$query->bindValue(':mail', $_['registerMail'], PDO::PARAM_STR);
		$query->bindValue(':date_registration', date('Y-m-d'), PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		$register_terminee = true;
	}
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<title>Inscription</title>
		<link rel="stylesheet" href="./template/bootstrap/css/bootstrap.min.css" />
		<style>
			body
			{
				padding-top: 60px;
				background-color: #eee;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<!-- Si l'inscription est terminé -->
				<?php if ($register_terminee) { ?>
					<div class="col-xs-12 col-sm-12 col-md-offset-3 col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">Inscription terminé !</div>
							<div class="panel-body">Votre compte à été crée avec succès, <strong><?php echo $registerUsername; ?></strong>. Un administrateur va valider votre inscription.<br />Aller à l'<a href="./">accueil</a>.</div>
						</div>
					</div>
					</div></div></body></html>
				<?php exit(); } ?>
				<!-- Si la session existe -->
				<?php if (isset($_SESSION['username'])) { ?>
					<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
						<div class="panel panel-default">
							<div class="panel-heading">Déjà inscrit !</div>
							<div class="panel-body">Vous êtes déjà inscrit. Aller à l'<a href="./">accueil</a>.</div>
						</div>
					</div>
					</div></div></body></html>
				<?php exit(); } ?>
				<!-- Sinon on affiche le formulaire -->
				<div class="col-xs-12 col-sm-12 col-md-offset-3 col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">Inscription</div>
						<form method="POST">
							<div class="panel-body">
								<?php if (isset($registerMessage)) echo '<div class="alert alert-danger">'.$registerMessage.'</div>'; ?>
								<h4>Informations générales</h4>
								<div class="form-group <?php if (isset($registerMessage) || isset($registerMessageUsername)) echo 'has-error'; ?>">
									<label>Nom d'utilisateur</label>
									<input type="text" class="form-control" name="registerUsername" value="<?php echo $registerUsername; ?>" />
								</div>
								<?php if (isset($registerMessageUsername)) echo '<div class="alert alert-danger">'.$registerMessageUsername.'</div>'; ?>
								<div class="form-group <?php if (isset($registerMessage) || isset($registerMessageMail)) echo 'has-error'; ?>">
									<label>Email</label>
									<input type="email" class="form-control" name="registerMail" value="<?php echo $registerMail; ?>" />
								</div>
								<?php if (isset($registerMessageMail)) echo '<div class="alert alert-danger">'.$registerMessageMail.'</div>'; ?>
								<div class="form-group <?php if (isset($registerMessage) || isset($registerMessagePassword)) echo 'has-error'; ?>">
									<label>Mot de passe</label>
									<input type="password" class="form-control" name="registerPassword1" value="<?php echo $registerPassword1; ?>" autocomplete="off" />
								</div>
								<div class="form-group <?php if (isset($registerMessage) || isset($registerMessagePassword)) echo 'has-error'; ?>">
									<label>Retapez le mot de passe</label>
									<input type="password" class="form-control" name="registerPassword2" value="<?php echo $registerPassword2; ?>" autocomplete="off" />
								</div>
								<?php if (isset($registerMessagePassword)) echo '<div class="alert alert-danger">'.$registerMessagePassword.'</div>'; ?>
								<br />
								<h4>Sécurité</h4>
								<div class="form-group <?php if (isset($registerMessage) || isset($registerMessageCaptcha)) echo 'has-error'; ?>">
									<label>Captcha</label>
									<div class="row">
										<div class="col-xs-9 col-sm-10 col-md-10">
											<input type="text" class="form-control" name="registerCaptcha" />
										</div>
										<div class="col-xs-3 col-sm-2 col-md-2">
											<img src="./includes/captcha.php" class="pull-right" alt="Code de vérification" />
										</div>
									</div>
								</div>
								<?php if (isset($registerMessageCaptcha)) echo '<div class="alert alert-danger">'.$registerMessageCaptcha.'</div>'; ?>
							</div>
							<div class="panel-footer clearfix">
								<a href="login.php" class="btn btn-primary">Se connecter</a>
								<button type="submit" class="btn btn-success pull-right" name="registerButton">S'inscrire</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>