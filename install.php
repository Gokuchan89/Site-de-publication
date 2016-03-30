<?php
	require_once('./includes/classFunctions.php');
	session_start();

	$install_terminee = false;
	
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	$whiteList = array('mysqlBase', 'mysqlHost', 'mysqlUsername', 'mysqlPassword');
	foreach($_ as $key=>&$val)
	{
		$val = in_array($key, $whiteList)
		? str_replace("'", "\'", $val)
		: Functions::secure($val);
	}

	// Valeurs par défaut, remplacées si une autre valeur est saisie
	foreach (array('root', 'mysqlBase', 'mysqlHost', 'mysqlUsername', 'mysqlPassword', 'username', 'password') as $var)
	{
		if (!empty($_[$var]))
		{
			$$var = $_[$var];
		} else {
			$$var = '';
		}
	}
	if (empty($root))
	{
		$root = str_replace(basename(__FILE__), '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
	}

	$lib_errors = 'Erreurs';
	$lib_success = 'Succès';
	
	// Pré-requis à l'installation
	if (@version_compare(PHP_VERSION, '5.1.0') <= 0)
	{
		$test[$lib_errors][] = sprintf('Votre version de PHP (%s) est trop ancienne, il est possible que certaines fonctionnalités du script comportent des dysfonctionnements.', PHP_VERSION);
	} else {
		$test[$lib_success][] = sprintf('Compatibilité version PHP (%s) : OK', PHP_VERSION);
	}
	if(!is_writable('./'))
	{
		$test[$lib_errors][] = sprintf('Écriture impossible dans le répertoire, veuillez ajouter les permissions en écriture sur tout le dossier (sudo chmod 777 -R %s, pensez à blinder les permissions par la suite).', str_replace(basename(__FILE__),'',__FILE__));
	} else {
		$test[$lib_success][] = 'Permissions sur le dossier courant : OK';
	}
	
	// On vérifie que tous les champs sont renseignés, ainsi que la disponibilité de la BDD.
	if(isset($_['installButton']))
	{
		if (empty($_['mysqlHost']) || empty($_['mysqlUsername']) || empty($_['mysqlPassword']) || empty($_['mysqlBase']))
		{
			$test[$lib_errors][] = 'Il est nécessaire de fournir toutes les informations de la base de donnée.';
		} else {
			if (!Functions::testDb($_['mysqlHost'], $_['mysqlUsername'], $_['mysqlPassword'], $_['mysqlBase']))
			{
				$test[$lib_errors][] = 'Connexion impossible à la base de données.';
			} else {
				$test[$lib_success][] = 'Connexion à la base de données : OK';
			}
		}
		if (empty($_['username']) || empty($_['password']))
		{
			$test[$lib_errors][] = 'Par sécurité, il est nécessaire de fournir un nom d\'utilisateur et un mot de passe.';
		}
	}
	
	if(isset($_['installButton']) && empty($test[$lib_errors]))
	{
		// Création du fichier mysqlConstants.php qui contiendra les infos de connexion à la BDD
		$constants = '
		<?php
			class CONSTANTS
			{
				public $DB_CONFIG			= \'mysql\';
				public $DB_HOST 			= \''.$mysqlHost.'\';
				public $DB_NAME 			= \''.$mysqlBase.'\';
				public $DB_USER 			= \''.$mysqlUsername.'\';
				public $DB_PASSWORD 		= \''.$mysqlPassword.'\';
			}
		?>';

		file_put_contents('./includes/mysqlConstants.php', $constants);
		if (!is_readable('./includes/mysqlConstants.php'))
		{
			die('"mysqlConstants.php" not found!');
		}
		
		if(!file_exists('profils'))
		{
			$old = umask(0);
			mkdir('profils', 0777);
			umask($old);
		}
		
		require_once('./includes/mysqlConstants.php');
		require_once('./includes/mysqlConnector.php');

		// Création de la table user
		$query = $db->query('CREATE TABLE IF NOT EXISTS site_user (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`username` varchar(225) NOT NULL,
			`password` varchar(225) NOT NULL,
			`mail` varchar(225) NOT NULL,
			`date_registration` date NOT NULL DEFAULT "0000-00-00",
			`date_lastlogin` datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
			`date_birthday` date NOT NULL DEFAULT "0000-00-00",
			`sex` enum("0", "1", "2") NOT NULL DEFAULT "0",
			`url_website` varchar(225) NOT NULL,
			`url_facebook` varchar(225) NOT NULL,
			`url_twitter` varchar(225) NOT NULL,
			`url_googleplus` varchar(225) NOT NULL,
			`country` varchar(225) NOT NULL,
			`avatar` varchar(225) NOT NULL DEFAULT "1.png",
			`theme` varchar(225) NOT NULL DEFAULT "bootstrap",
			`rank` enum("1", "2", "3") NOT NULL DEFAULT "2",
			`access` enum("0", "1") NOT NULL DEFAULT "0",
			PRIMARY KEY (`id`)
		) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
		$query->closeCursor();
		
		// Insertion des données de l'utilisateur (administrateur) dans la table user
		$query = $db->prepare('INSERT INTO site_user (`username`, `password`, `date_registration`, `rank`, `access`) VALUES (:username, :password, :date_registration, :rank, :access)');
		$query->bindValue(':username', $username, PDO::PARAM_STR);
		$query->bindValue(':password', md5($password), PDO::PARAM_STR);
		$query->bindValue(':date_registration', date('Y-m-d'), PDO::PARAM_INT);
		$query->bindValue(':rank', '3', PDO::PARAM_INT);
		$query->bindValue(':access', '1', PDO::PARAM_INT);
		$query->execute();
		$query->closeCursor();

		// Création de la table category
		$query = $db->query('CREATE TABLE IF NOT EXISTS site_category (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(225) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
		$query->closeCursor();

		// Création de la table menu
		$query = $db->query('CREATE TABLE IF NOT EXISTS site_menu (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(225) NOT NULL,
			`icon` varchar(225) NOT NULL,
			`category` int(11) NOT NULL,
			`table` varchar(225) NOT NULL,
			`type` enum("autre", "jeuxvideo", "livre", "musique", "video") NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
		$query->closeCursor();

		// Création de la table configuration
		$query = $db->query('CREATE TABLE IF NOT EXISTS site_configuration (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`key` varchar(225) NOT NULL,
			`value` varchar(225) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
		$query->closeCursor();

		// Insertion des données dans la table configuration
		$query = $db->prepare('INSERT INTO site_configuration (`key`, `value`) VALUES (:key, :value)');
		$query->bindValue(':key', 'open', PDO::PARAM_STR);
		$query->bindValue(':value', '0', PDO::PARAM_STR);
		$query->execute();
		$query->closeCursor();
		
		$query = $db->prepare('INSERT INTO site_configuration (`key`, `value`) VALUES (:key, :value)');
		$query->bindValue(':key', 'root', PDO::PARAM_STR);
		$query->bindValue(':value', $root, PDO::PARAM_STR);
		$query->execute();
		$query->closeCursor();
		
		$query = $db->prepare('INSERT INTO site_configuration (`key`, `value`) VALUES (:key, :value)');
		$query->bindValue(':key', 'title', PDO::PARAM_STR);
		$query->bindValue(':value', 'Site', PDO::PARAM_STR);
		$query->execute();
		$query->closeCursor();
		
		$query = $db->prepare('INSERT INTO site_configuration (`key`, `value`) VALUES (:key, :value)');
		$query->bindValue(':key', 'lastaddMax', PDO::PARAM_STR);
		$query->bindValue(':value', '6', PDO::PARAM_STR);
		$query->execute();
		$query->closeCursor();
		
		$query = $db->prepare('INSERT INTO site_configuration (`key`, `value`) VALUES (:key, :value)');
		$query->bindValue(':key', 'avatarMaxWidth', PDO::PARAM_STR);
		$query->bindValue(':value', '128', PDO::PARAM_STR);
		$query->execute();
		$query->closeCursor();
		
		$query = $db->prepare('INSERT INTO site_configuration (`key`, `value`) VALUES (:key, :value)');
		$query->bindValue(':key', 'avatarMaxHeight', PDO::PARAM_STR);
		$query->bindValue(':value', '128', PDO::PARAM_STR);
		$query->execute();
		$query->closeCursor();
		
		$query = $db->prepare('INSERT INTO site_configuration (`key`, `value`) VALUES (:key, :value)');
		$query->bindValue(':key', 'avatarMaxWeight', PDO::PARAM_STR);
		$query->bindValue(':value', '51200', PDO::PARAM_STR);
		$query->execute();
		$query->closeCursor();
		
		$_SESSION['username'] = $username;
			
		$install_terminee = true;
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
		<title>Installation</title>
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
				<?php if ($install_terminee) { ?>
					<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2">
						<div class="panel panel-default">
							<div class="panel-heading">Installation terminée !</div>
							<div class="panel-body">L'installation est terminée. Vous pouvez accéder à votre <a href="./?op=settings&tab=3">site</a>.</div>
						</div>
					</div>
					</div></div></body></html>
				<?php exit(); } ?>
				<?php if (file_exists('./includes/mysqlConstants.php')) { ?>
					<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2">
						<div class="panel panel-default">
							<div class="panel-heading">Installation terminée !</div>
							<div class="panel-body">Votre site est déjà configuré. Supprimez ou renommez le fichier de configuration.</div>
						</div>
					</div>
					</div></div></body></html>
				<?php exit(); } ?>
				<div class="col-xs-12 col-sm-12 col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">Pré-requis à l'installation</div>
						<div class="panel-body">
							<?php
								foreach($test as $type=>$messages)
								{
									$class = 'alert ';
									$class .= $lib_errors==$type?'alert-danger':'alert-success';
									foreach ($messages as $message)
									{
										echo '<div class="'.$class.'">'.$message.'</div>';
									}
								}
							?>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">Installation</div>
						<form method="POST" action="install.php">
							<div class="panel-body">
								<h4>Général</h4>
								<div class="form-group">
									<label>Racine du projet</label>
									<input type="text" class="form-control" name="root" value="<?php echo $root; ?>" />
								</div>
								<br/>
								<h4>Base de donnée</h4>
								<div class="form-group">
									<label>Base</label>
									<input type="text" class="form-control" name="mysqlBase" value="<?php echo $mysqlBase; ?>" placeholder="A créer avant" />
								</div>
								<div class="form-group">
									<label>Hôte</label>
									<input type="text" class="form-control" name="mysqlHost" value="<?php echo $mysqlHost; ?>" placeholder="Généralement 'localhost'" />
								</div>
								<div class="form-group">
									<label>Nom d'utilisateur</label>
									<input type="text" class="form-control" name="mysqlUsername" value="<?php echo $mysqlUsername; ?>" />
								</div>
								<div class="form-group">
									<label>Mot de passe</label>
									<input type="text" class="form-control" name="mysqlPassword" value="<?php echo $mysqlPassword; ?>" placeholder="Sera affiché en clair" autocomplete="off" />
								</div>
								<br/>
								<h4>Administrateur</h4>
								<div class="form-group">
									<label>Nom d'utilisateur</label>
									<input type="text" class="form-control" name="username" value="<?php echo $username; ?>" />
								</div>
								<div class="form-group">
									<label>Mot de passe</label>
									<input type="text" class="form-control" name="password" value="<?php echo $password; ?>" placeholder="Sera affiché en clair" autocomplete="off" />
								</div>
							</div>
							<div class="panel-footer clearfix">
								<button type="submit" id="installButton" name="installButton" class="btn btn-success pull-right">Lancer l'installation</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>