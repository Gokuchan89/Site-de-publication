<?php
	include("../class/_classLoader.php");

	$install_terminee = false;
	$lib_errors = "Erreurs";

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	$whiteList = array("db_server", "db_port", "db_name", "db_username", "db_password");
	foreach ($_ as $key => &$val)
	{
		$val = in_array($key, $whiteList)
		? str_replace("'", "\'", $val)
		: Functions::secure($val);
	}

	// Valeurs par défaut, remplacées si une autre valeur est saisie
	foreach (array("db_server", "db_port", "db_username", "db_password", "db_name", "admin_username", "admin_email", "admin_password") as $var)
	{
		if (!empty($_[$var]))
		{
			$$var = $_[$var];
		} else {
			$$var = "";
		}
	}

	// Pré-requis à l'installation
	if (@version_compare(PHP_VERSION, "5.1.0") <= 0)
	{
		$test[$lib_errors][] = "Votre version de PHP ".PHP_VERSION." est trop ancienne, il est possible que certaines fonctionnalités du script comportent des dysfonctionnements.";
	}
	if (!is_writable("./"))
	{
		$test[$lib_errors][] = "Écriture impossible dans le répertoire, veuillez ajouter les permissions en écriture sur tout le dossier (sudo chmod 777 -R, pensez à blinder les permissions par la suite).";
	}

	if (isset($_['step1']) && $_['step1'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_['db_server']) && !empty($_['db_username']) && !empty($_['db_password']) && !empty($_['db_name']))
		{
			if (Functions::testDb($_['db_server'], $_['db_port'], $_['db_name'], $_['db_username'], $_['db_password']))
			{
				if (!empty($_['admin_username']) && !empty($_['admin_email']) && !empty($_['admin_password']))
				{
					// MySQL
					$db_server = $_['db_server'];
					$db_port = $_['db_port'];
					$db_name = $_['db_name'];
					$db_username = $_['db_username'];
					$db_password = $_['db_password'];

					// Admin
					$admin_username = $_['admin_username'];
					$admin_email = $_['admin_email'];
					$admin_password = $_['admin_password'];

					if (file_exists("../data/db_config.inc.php"))
					{
						unlink("../data/db_config.inc.php");
					}
					if (!file_exists("../data/db_config.inc.php"))
					{
						// Création du fichier db_config.inc.php dans le dossier data
						$fileDBConfigContent = "<?php\n";
						$fileDBConfigContent = $fileDBConfigContent."// Paramètres de connexion à la base de données\n";
						$fileDBConfigContent = $fileDBConfigContent."\$db['server'] = \"".$db_server."\"; // Adresse du serveur MySQL\n";
						$fileDBConfigContent = $fileDBConfigContent."\$db['port'] = \"".$db_port."\"; // Port du serveur MySQL (laisser vide si le port standard est utilisé)\n";
						$fileDBConfigContent = $fileDBConfigContent."\$db['name'] = \"".$db_name."\"; // Nom  de la base de données\n";
						$fileDBConfigContent = $fileDBConfigContent."\$db['username'] = \"".$db_username."\"; // Utilisateur de la base de données\n";
						$fileDBConfigContent = $fileDBConfigContent."\$db['password'] = \"".$db_password."\"; // Mot de passe de la base de données\n";
						$fileDBConfigContent = $fileDBConfigContent."?>";

						$fileDBConfig = fopen("../data/db_config.inc.php", "a+");
						fputs($fileDBConfig, $fileDBConfigContent);
						fclose($fileDBConfig);
					}
					if (file_exists("../data/db_config.inc.php"))
					{
						if (file_exists("../profils"))
						{
							unlink("../profils/index.php");
							rmdir("../profils");
						}
						if (!file_exists("../profils"))
						{
							// Création du dossier profils
							$old = umask(0);
							mkdir("../profils", 0777);
							umask($old);

							// Création du fichier index.php dans le dossier profils
							$fileIndexContent = "<?php header(\"location: ../\"); ?>";

							$fileIndex = fopen("../profils/index.php", "a+");
							fputs($fileIndex, $fileIndexContent);
							fclose($fileIndex);
						}
						if (file_exists("../profils"))
						{
							// Création des bases de données
							$database = file_get_contents("./database.sql");
							$mysql = new MySQL();
							$mysql->requeteFichierSQL($database);

							$site_category = $mysql->testPresenceTable($db_name, "site_category");
							$site_list = $mysql->testPresenceTable($db_name, "site_list");
							$site_log = $mysql->testPresenceTable($db_name, "site_log");
							$site_log_activite = $mysql->testPresenceTable($db_name, "site_log_activite");
							$site_menu = $mysql->testPresenceTable($db_name, "site_menu");
							$site_setting = $mysql->testPresenceTable($db_name, "site_setting");
							$site_user = $mysql->testPresenceTable($db_name, "site_user");

							if ($site_category && $site_list && $site_log && $site_log_activite && $site_menu && $site_setting && $site_user)
							{
								$admin = new User();
								$admin->setName($admin_username);
								$admin->setUsername($admin_username);
								$admin->setPassword($admin_password);
								$admin->setEmail($admin_email);
								$admin->setDateregistration(time());
								$admin->setAdmin(1);
								$admin->setAccess(1);
								$admin->saveUser();

								$admin_presence = $admin->testPresenceUser($admin_username);
								if ($admin_presence)
								{
									header("location: ./step2.php");
									exit();
								} else {
									$test[$lib_errors][] = "Impossible d'ajouter le compte administrateur.";
								}
							} else {
								$test[$lib_errors][] = "Impossible de créer les tables.";
							}
						} else {
							$test[$lib_errors][] = "Impossible de créer le dossier profils à la racine.";
						}
					} else {
						$test[$lib_errors][] = "Impossible de créer le fichier de configuration dans le dossier data.";
					}
				} else {
					$test[$lib_errors][] = "Il est nécessaire de fournir un identifiant, un email et un mot de passe pour le compte administrateur.";
				}
			} else {
				$test[$lib_errors][] = "Connexion impossible à la base de données.";
			}
		} else {
			$test[$lib_errors][] = "Il est nécessaire de fournir toutes les informations sur la base de données.";
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Etape 1 : Installation</title>
		<!-- BOOTSTRAP 3.3.7 -->
		<link rel="stylesheet" href="../template/bootstrap/css/bootstrap.min.css">
		<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
		<link rel="stylesheet" href="../template/bootstrap/plugins/bootstrap-validator/css/bootstrap-validator.min.css">
		<!-- FONT-AWESOME 4.6.3 -->
		<link rel="stylesheet" href="../template/bootstrap/plugins/font-awesome/css/font-awesome.min.css" />
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<style>
			body
			{
				padding-top: 100px;
				background-color: #eee;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<!-- INFORMATIONS -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Informations</h4>
						</div>
						<div class="modal-body">
							<p>Veillez à bien noter les informations relatives à : l'adresse du serveur, le nom de la base de données et l'identifiant (et le mot de passe) de votre base de données fournit par votre hébergeur.</p><br/>
							<p>On va, tout d’abord, spécifier les informations de base de données. A noter que certains hébergeurs n’acceptent pas que l'on se connecte à leur base de données via l'adresse "localhost". Dans ce cas, n’hésitez pas à solliciter votre hébergeur (ou à regarder dans le panel admin) pour connaître l'adresse du serveur à spécifier.</p>
							<p class="text-center"><img src="./img/step1-1.jpg" /></p><br/>
							<p>Puis, on définit le compte administrateur.</p>
							<p class="text-center"><img src="./img/step1-2.jpg" /></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
						</div>
					</div>
				</div>
			</div>
			<!-- FORMULAIRE -->
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
					<div class="panel panel-default">
						<div class="panel-heading clearfix">
							<h4 class="panel-title pull-left" style="padding-top: 7.5px;">ETAPE 1 : INSTALLATION</h4>
							<div class="pull-right"><button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-question"></i></button></div>
						</div>
						<form method="post" action="./step1.php" id="step1Form">
							<input type="hidden" name="step1" value="1" />
							<div class="panel-body">
								<?php
									if (!empty($test[$lib_errors]))
									{
										foreach ($test as $type=>$messages)
										{
											foreach ($messages as $message)
											{
												echo "<div class=\"alert alert-danger\">".$message."</div>";
											}
										}
									}
								?>
								<h4>Base de données</h4>
								<div class="form-group has-feedback">
									<input type="text" class="form-control" name="db_server" value="<?php echo $db_server; ?>" placeholder="Adresse du serveur" autofocus required />
									<span class="form-control-feedback"><i class="fa fa-globe"></i></span>
								</div>
								<div class="form-group has-feedback">
									<input type="text" class="form-control" name="db_port" value="<?php echo $db_port; ?>" placeholder="Port (falcultatif)" />
									<span class="form-control-feedback"><i class="fa fa-gear"></i></span>
								</div>
								<div class="form-group has-feedback">
									<input type="text" class="form-control" name="db_name" value="<?php echo $db_name; ?>" placeholder="Nom de la base de données" required />
									<span class="form-control-feedback"><i class="fa fa-database"></i></span>
								</div>
								<div class="form-group has-feedback">
									<input type="text" class="form-control" name="db_username" value="<?php echo $db_username; ?>" placeholder="Utilisateur" required />
									<span class="form-control-feedback"><i class="fa fa-user"></i></span>
								</div>
								<div class="form-group has-feedback">
									<input type="password" class="form-control" name="db_password" value="<?php echo $db_password; ?>" placeholder="Mot de passe" autocomplete="off" required />
									<span class="form-control-feedback"><i class="fa fa-lock"></i></span>
								</div>
								<hr>
								<h4>Administrateur</h4>
								<div class="form-group has-feedback">
									<input type="text" class="form-control" name="admin_username" value="<?php echo $admin_username; ?>" placeholder="Identifiant" required />
									<span class="form-control-feedback"><i class="fa fa-user"></i></span>
								</div>
								<div class="form-group has-feedback">
									<input type="email" class="form-control" name="admin_email" value="<?php echo $admin_email; ?>" placeholder="Email" required />
									<span class="form-control-feedback"><i class="fa fa-envelope"></i></span>
								</div>
								<div class="form-group has-feedback">
									<input type="password" class="form-control" name="admin_password" value="<?php echo $db_password; ?>" placeholder="Mot de passe" autocomplete="off" required />
									<span class="form-control-feedback"><i class="fa fa-lock"></i></span>
								</div>
							</div>
							<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Etape suivante</button></div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- JQUERY 3.1.1 -->
		<script src="../template/bootstrap/js/jquery.min.js"></script>
		<!-- BOOTSTRAP 3.3.7 -->
		<script src="../template/bootstrap/js/bootstrap.min.js"></script>
		<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
		<script src="../template/bootstrap/plugins/bootstrap-validator/js/bootstrap-validator.min.js"></script>
		<script src="../template/bootstrap/plugins/bootstrap-validator/js/i18n/fr_FR.js"></script>
		<script>
			$("#step1Form").bootstrapValidator(
			{
				locale: "fr_FR",
				fields:
				{
					db_server:
					{
						validators:
						{
							notEmpty:
							{
							}
						}
					},
					db_port:
					{
						validators:
						{
							regexp:
							{
								regexp: /^[0-9]+$/
							}
						}
					},
					db_name:
					{
						validators:
						{
							notEmpty:
							{
							}
						}
					},
					db_username:
					{
						validators:
						{
							notEmpty:
							{
							}
						}
					},
					db_password:
					{
						validators:
						{
							notEmpty:
							{
							}
						}
					},
					admin_username:
					{
						validators:
						{
							notEmpty:
							{
							},
							stringLength:
							{
								min: 4,
								max: 30
							},
							regexp:
							{
								regexp: /^[a-zA-Z0-9]+$/
							}
						}
					},
					admin_email:
					{
						validators:
						{
							emailAdress:
							{
							},
							notEmpty:
							{
							}
						}
					},
					admin_password:
					{
						validators:
						{
							notEmpty:
							{
							},
							stringLength:
							{
								min: 6,
								max: 30
							}
						}
					}
				}
			});
		</script>
	</body>
</html>