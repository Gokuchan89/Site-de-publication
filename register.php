<?php
	if(!file_exists('./includes/mysqlConfig.php'))
	{
		header('location: ./install.php');
		exit();
	}
	
	require_once('./includes/classFunctions.php');
	session_start();

	$register_terminee = false;
	
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach($_ as $key=>&$val)
	{
		Functions::secure($val);
	}

	// Valeurs par défaut, remplacées si une autre valeur est saisie
	foreach (array('username', 'mail', 'password1', 'password2') as $var)
	{
		if (!empty($_[$var]))
		{
			$$var = $_[$var];
		} else {
			$$var = '';
		}
	}

	$lib_errors = 'Erreurs';
	$lib_success = 'Succès';
	
	// On vérifie que tous les champs sont renseignés et non utilisés
	if(isset($_['registerButton']))
	{
		if (empty($_['username']) || empty($_['mail']) || empty($_['password1']) || empty($_['password2']) || empty($_['captcha']))
		{
			$registerMessage[$lib_errors][] = 'Il est nécessaire de fournir un nom d\'utilisateur, un email, un mot de passe et le code de sécurité.';
		}

		require_once('./includes/mysqlConfig.php');
		require_once('./includes/mysqlConnector.php');

		// Vérification de la disponibilité du nom de l'utilisateur
		$query = $db->prepare('SELECT COUNT(id) FROM site_user WHERE `username` = :username');
		$query->bindValue(':username', $_['username'], PDO::PARAM_STR);
		$query->execute();
		$user_free = $query->fetchColumn();
		$query->CloseCursor();
		if ($user_free > 0 && empty($registerMessage[$lib_errors]))
		{
			$registerMessage[$lib_errors][] = 'Le nom d\'utilisateur est déjà utilisé par un autre membre.';
		}

		// Vérification de la disponibilité de l'email
		$query = $db->prepare('SELECT COUNT(id) FROM site_user WHERE `mail`=:mail');
		$query->bindValue(':mail', $_['mail'], PDO::PARAM_STR);
		$query->execute();
		$mail_free = $query->fetchColumn();
		$query->CloseCursor();
		if ($mail_free > 0 && empty($registerMessage[$lib_errors]))
		{
			$registerMessage[$lib_errors][] = 'L\'email est déjà utilisée par un autre membre.';
		}
		
		// Vérification des 2 mots de passe
		if ($_['password1'] != $_['password2'] && empty($registerMessage[$lib_errors]))
		{
			$registerMessage[$lib_errors][] = 'Le mot de passe et le mot de passe de confirmation ne sont pas identiques.';
		}
		
		// Vérification du captcha
		if ($_['captcha'] != $_SESSION['aleat_nbr'] && empty($registerMessage[$lib_errors]))
		{
			$registerMessage[$lib_errors][] = 'Le code de sécurité ne correspond pas à l\'image.';
		}
	}
	
	if(isset($_['registerButton']) && empty($registerMessage[$lib_errors]))
	{
		$query = $db->prepare('INSERT INTO site_user (`username`, `password`, `mail`, `date_registration`) VALUES (:username, :password, :mail, :date_registration)');
		$query->bindValue(':username', $_['username'], PDO::PARAM_STR);
		$query->bindValue(':password', md5($_['password1']), PDO::PARAM_STR);
		$query->bindValue(':mail', $_['mail'], PDO::PARAM_STR);
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
		<link rel="stylesheet" href="template/bootstrap/css/bootstrap.min.css" />
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
				<?php if ($register_terminee) { ?>
					<div class="col-xs-12 col-sm-12 col-md-offset-3 col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">Enregistrement terminé !</div>
							<div class="panel-body"><?php echo sprintf('Votre compte à été crée avec succès <strong>%s</strong>. Un administrateur va valider votre inscription.<br />Retourner à l\'<a href="%s">accueil</a>.', $username, './'); ?></div>
						</div>
					</div>
					</div></div></body></html>
				<?php exit(); } ?>
				<div class="col-xs-12 col-sm-12 col-md-offset-3 col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">Inscription</div>
						<form method="POST" action="register.php">
							<div class="panel-body">
								<?php
									if(isset($registerMessage[$lib_errors]))
									{
										foreach($registerMessage as $type=>$messages)
										{
											$class = 'alert ';
											$class .= $lib_errors==$type?'alert-danger':'alert-success';
											foreach ($messages as $message)
											{
												echo '<div class="'.$class.'">'.$message.'</div>';
											}
										}
									}
								?>
								<h4>Informations générales</h4>
								<div class="form-group">
									<label>Nom d'utilisateur</label>
									<input type="text" class="form-control" name="username" value="<?php echo $username; ?>" />
								</div>
								<div class="form-group">
									<label>Email</label>
									<input type="email" class="form-control" name="mail" value="<?php echo $mail; ?>" />
								</div>
								<div class="form-group">
									<label>Mot de passe</label>
									<input type="password" class="form-control" name="password1" value="<?php echo $password1; ?>" autocomplete="off" />
								</div>
								<div class="form-group">
									<label>Retapez le mot de passe</label>
									<input type="password" class="form-control" name="password2" value="<?php echo $password2; ?>" autocomplete="off" />
								</div>
								<br/>
								<h4>Sécurité</h4>
								<div class="form-group">
									<label>Captcha</label>
									<div class="row">
										<div class="col-xs-9 col-sm-10 col-md-10">
											<input type="text" class="form-control" name="captcha" />
										</div>
										<div class="col-xs-3 col-sm-2 col-md-2">
											<img src="./includes/captcha.php" class="pull-right" alt="Code de vérification" />
										</div>
									</div>
								</div>
							</div>
							<div class="panel-footer clearfix">
								<a href="login.php" class="btn btn-primary">Se connecter</a>
								<button type="submit" name="registerButton" class="btn btn-success pull-right">S'inscrire</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>