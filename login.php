<?php
	if(!file_exists('./includes/mysqlConfig.php'))
	{
		header('location: install.php');
		exit();
	}
	
	require_once('./includes/classFunctions.php');
	session_start();
	
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach($_ as $key=>&$val)
	{
		Functions::secure($val);
	}

	// Valeurs par défaut, remplacées si une autre valeur est saisie
	foreach (array('username', 'password') as $var)
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
	
	// On vérifie que tous les champs sont renseignés et correct
	if(isset($_['loginButton']))
	{
		if (empty($_['username']) || empty($_['password']))
		{
			$loginMessage[$lib_errors][] = 'Il est nécessaire de fournir un nom d\'utilisateur et un mot de passe.';
		}
		
		require_once('./includes/mysqlConfig.php');
		require_once('./includes/mysqlConnector.php');
		
		$query = $db->prepare('SELECT `username`, `password`, `access` FROM site_user WHERE `username` = :username');
		$query->bindValue(':username', $_['username'], PDO::PARAM_STR);
		$query->execute();
		$login_verif = $query->fetch();
		$query->CloseCursor();

		if ($login_verif['username'] != $_['username'] && empty($loginMessage[$lib_errors]))
		{
			$loginMessage[$lib_errors][] = 'Ce compte n\'existe pas.';
		}
		if ($login_verif['password'] != md5($password) && empty($loginMessage[$lib_errors]))
		{
			$loginMessage[$lib_errors][] = 'Ce mot de passe est incorrect.';
		}
		if ($login_verif['access'] == 0 && empty($loginMessage[$lib_errors]))
		{
			$loginMessage[$lib_errors][] = 'Ce compte n\'est pas encore activé.';
		}
	}
	
	if(isset($_['loginButton']) && empty($loginMessage[$lib_errors]))
	{
		$_SESSION['username'] = $_['username'];
		header('location: ./');
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
				<?php if (isset($_SESSION['username'])) { ?>
					<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
						<div class="panel panel-default">
							<div class="panel-heading">Déjà identifié !</div>
							<div class="panel-body"><?php echo sprintf('Vous êtes déjà identifié. Veuillez retourner sur le <a href="%s">site</a>.', './'); ?></div>
						</div>
					</div>
					</div></div></body></html>
				<?php exit(); } ?>
				<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
					<div class="panel panel-default">
						<div class="panel-heading">Connexion</div>
						<form method="POST">
							<div class="panel-body">
								<?php
									if(isset($loginMessage[$lib_errors]))
									{
										foreach($loginMessage as $type=>$messages)
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
								<div class="form-group">
									<label>Nom d'utilisateur</label>
									<input type="text" class="form-control" name="username" value="<?php echo $username; ?>" />
								</div>
								<div class="form-group">
									<label>Mot de passe</label>
									<input type="password" class="form-control" name="password" value="<?php echo $password; ?>" autocomplete="off" />
								</div>
							</div>
							<div class="panel-footer clearfix">
								<a href="register.php" class="btn btn-primary">S'inscrire</a>
								<button type="submit" name="loginButton" class="btn btn-success pull-right">Se connecter</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>