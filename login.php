<?php
	if (!file_exists('./includes/mysqlConstants.php'))
	{
		header('location: ./install.php');
		exit();
	}

	require_once('./includes/classFunctions.php');
	session_start();

	$i = 0;
	$install_terminee = false;

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key=>&$val)
	{
		Functions::secure($val);
	}

	// Valeurs par défaut, remplacées si une autre valeur est saisie
	foreach (array('loginUsername', 'loginPassword') as $var)
	{
		if (!empty($_[$var]))
		{
			$$var = $_[$var];
		} else {
			$$var = '';
		}
	}

	if (isset($_['loginButton']))
	{
		// Vérification si tous les champs sont renseignés
		if (empty($_['loginUsername']) || empty($_['loginPassword']))
		{
			$loginMessage = 'Veuillez renseigner un nom d\'utilisateur et/ou un mot de passe.';
			$i++;
		}

		require_once('./includes/mysqlConstants.php');
		require_once('./includes/mysqlConnector.php');

		$query = $db->prepare('SELECT `username`, `password`, `access` FROM `site_user` WHERE `username` = :username');
		$query->bindValue(':username', $_['loginUsername'], PDO::PARAM_STR);
		$query->execute();
		$loginVerif = $query->fetch();
		$query->CloseCursor();

		// Vérification si le nom d'utilisateur est présent dans la table site_user et si le mot de passe est valide
		if (($loginVerif['username'] != $_['loginUsername'] || $loginVerif['password'] != md5($_['loginPassword'])) && $i == 0)
		{
			$loginMessage = 'Nom d\'utilisateur et/ou mot de passe invalide.';
			$i++;
		}

		// Vérification si l'utilisateur a accès au site
		if ($loginVerif['access'] == 0 && $i == 0)
		{
			$loginMessage = 'Votre compte n\'est pas encore activé.';
			$i++;
		}
	}

	// Pas d'erreur, l'utilisateur se connecte
	if (isset($_['loginButton']) && $i == 0)
	{
		$_SESSION['username'] = $_['loginUsername'];

		$login_terminee = true;
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
		<title>Connexion</title>
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
				<!-- Si la connexion est réussi -->
				<?php if ($login_terminee) { ?>
					<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
						<div class="panel panel-default">
							<div class="panel-heading">Identification réussi !</div>
							<div class="panel-body">Vous êtes identifié. Aller à l'<a href="./">accueil</a>.</div>
						</div>
					</div>
					</div></div></body></html>
				<?php exit(); } ?>
				<!-- Si la session existe -->
				<?php if (isset($_SESSION['username'])) { ?>
					<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
						<div class="panel panel-default">
							<div class="panel-heading">Déjà identifié !</div>
							<div class="panel-body">Vous êtes déjà identifié. Aller à l'<a href="./">accueil</a>.</div>
						</div>
					</div>
					</div></div></body></html>
				<?php exit(); } ?>
				<!-- Sinon on affiche le formulaire -->
				<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
					<div class="panel panel-default">
						<div class="panel-heading">Connexion</div>
						<form method="POST">
							<div class="panel-body">
								<?php if (isset($loginMessage)) echo '<div class="alert alert-danger">'.$loginMessage.'</div>'; ?>
								<div class="form-group <?php if (isset($loginMessage)) echo 'has-error'; ?>">
									<label>Nom d'utilisateur</label>
									<input type="text" class="form-control" name="loginUsername" value="<?php echo $loginUsername; ?>" />
								</div>
								<div class="form-group <?php if (isset($loginMessage)) echo 'has-error'; ?>">
									<label>Mot de passe</label>
									<input type="password" class="form-control" name="loginPassword" value="<?php echo $loginPassword; ?>" autocomplete="off" />
								</div>
							</div>
							<div class="panel-footer clearfix">
								<a href="register.php" class="btn btn-primary">S'inscrire</a>
								<button type="submit" class="btn btn-success pull-right" name="loginButton">Se connecter</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>