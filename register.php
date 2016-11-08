<?php
	if (!file_exists("./data/db_config.inc.php"))
	{
		header("location: ./");
		exit();
	}
	
	session_start();
	
	include("./class/_classLoader.php");
	
	$setting_registration = new Setting();
	$setting_registration->getSettingDBKey('registration');
	if (isset($_SESSION['username']) || $setting_registration->getValue() == 0)
	{
		header("location: ./");
		exit();
	}

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key => &$val)
	{
		Functions::secure($val);
	}
	
	$lib_errors = "Erreurs";
	$lib_success = "Succès";
	
	if (isset($_['registerButton']) && empty($test[$lib_errors]))
	{
		if (!empty($_['register_username']) && !empty($_['register_email']) && !empty($_['register_password1']) && !empty($_['register_password2']) && !empty($_['register_captcha']))
		{
			// Utilisateur
			$register_username = $_['register_username'];
			$register_password1 = $_['register_password1'];
			$register_password2 = $_['register_password2'];
			$register_email = $_['register_email'];
			$register_date = time();
			$register_admin = "0";
			$register_access = "0";
			
			// Captcha
			$register_captcha = $_['register_captcha'];
			$verif_captcha = $_SESSION['aleat_nbr'];
			
			if ($register_password1 == $register_password2)
			{
				if ($register_captcha == $verif_captcha)
				{
					$user = new User();
					$user = $user->TestPresenceUser($register_username);
					if (!$user)
					{
						$user = new User();
						$user->setName($register_username);
						$user->setUsername($register_username);
						$user->setPassword($register_password1);
						$user->setEmail($register_email);
						$user->setDateregistration($register_date);
						$user->setAdmin($register_admin);
						$user->setAccess($register_access);
						$user->saveUser();
				
						$log = new Log_activite();
						$log->setUsername($register_username);
						$log->setAction("Inscription utilisateur");
						$log->setComment("L'utilisateur ".$register_username." s'est inscrit.");
						$log->saveLog_activite();
					
						$test[$lib_success][] = "<strong>Inscription réussie !</strong><br/>Un administrateur va valider votre inscription ".$register_username.".";
					} else {
						$test[$lib_errors][] = "Impossible d'ajouter le compte.";
					}
				} else {
					$test[$lib_errors][] = "Le code de sécurité ne correspond pas à l'image.";
				}
			} else {
				$test[$lib_errors][] = "Les mots de passe ne correspondent pas.";
			}
		} else {
			$test[$lib_errors][] = 'Il est nécessaire de fournir un identifiant, un email, un mot de passe et le code de sécurité.';
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<title>Inscription</title>
		<!-- BOOTSTRAP 3.3.7 -->
		<link rel="stylesheet" href="./template/bootstrap/css/bootstrap.min.css" />
		<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
		<link rel="stylesheet" href="./template/bootstrap/plugins/bootstrap-validator/css/bootstrap-validator.min.css">
		<!-- FONT-AWESOME 4.6.3 -->
		<link rel="stylesheet" href="./template/bootstrap/plugins/font-awesome/css/font-awesome.min.css" />
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
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
					<div class="panel panel-default">
						<div class="panel-heading">Inscription</div>
						<form method="post" id="registerForm" action="register.php">
							<div class="panel-body">
								<?php
									if (!empty($test[$lib_errors]) || !empty($test[$lib_success]))
									{
										foreach ($test as $type=>$messages)
										{
											foreach ($messages as $message)
											{
												$class = "alert ";
												$class .= $lib_errors==$type?"alert-danger":"alert-success";
												echo "<div class=\"".$class."\">".$message."</div>";
											}
										}
									}
								?>
								<div class="form-group has-feedback">
									<input type="text" class="form-control" name="register_username" placeholder="Identifiant" autofocus required />
									<span class="form-control-feedback"><i class="fa fa-user"></i></span>
									<span id="helpBlock" class="help-block">Vous avez déjà un compte ? <a href="./login.php" class="text-center">Se connecter</a></span>
								</div>
								<div class="form-group has-feedback">
									<input type="email" class="form-control" name="register_email" placeholder="Email" required />
									<span class="form-control-feedback"><i class="fa fa-envelope"></i></span>
								</div>
								<div class="form-group has-feedback">
									<input type="password" class="form-control" name="register_password1" placeholder="Mot de passe" autocomplete="off" required />
									<span class="form-control-feedback"><i class="fa fa-lock"></i></span>
								</div>
								<div class="form-group has-feedback">
									<input type="password" class="form-control" name="register_password2" placeholder="Retapez le mot de passe" autocomplete="off" required />
									<span class="form-control-feedback"><i class="fa fa-lock"></i></span>
								</div>
								<div class="row">
									<div class="col-xs-9 col-sm-10 col-md-10">
										<div class="form-group has-feedback">
											<input type="number" class="form-control" name="register_captcha" placeholder="Captcha" required />
											<span class="form-control-feedback"><i class="fa fa-key"></i></span>
										</div>
									</div>
									<div class="col-xs-3 col-sm-2 col-md-2">
										<img src="./data/captcha.php" class="pull-right" alt="Code de vérification" />
									</div>
								</div>
							</div>
							<div class="panel-footer clearfix">
								<button type="submit" class="btn btn-success pull-right" name="registerButton">S'inscrire</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- JQUERY 3.1.1 -->
		<script src="./template/bootstrap/js/jquery.min.js"></script>
		<!-- BOOTSTRAP 3.3.7 -->
		<script src="./template/bootstrap/js/bootstrap.min.js"></script>
		<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
		<script src="./template/bootstrap/plugins/bootstrap-validator/js/bootstrap-validator.min.js"></script>
		<script src="./template/bootstrap/plugins/bootstrap-validator/js/i18n/fr_FR.js"></script>
		<script>
			$(document).ready(function()
			{
				$("#registerForm").bootstrapValidator(
				{
					locale: "fr_FR",
					fields:
					{
						register_username:
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
								},
								remote:
								{
									message: "Cet identifiant est déjà utilisé",
									type: "POST",
									url: "./data/verif_username.php",
									data: function(validator)
									{
										return {
											register_username: validator.getFieldElements("register_username").val()
										};
									}
								}
							}
						},
						register_email:
						{
							validators:
							{
								emailAdress:
								{
								},
								notEmpty:
								{
								},
								remote:
								{
									message: "Cette adresse email est déjà utilisée",
									type: "POST",
									url: "./data/verif_email.php",
									data: function(validator)
									{
										return {
											register_email: validator.getFieldElements("register_email").val()
										};
									}
								}
							}
						},
						register_password1:
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
								},
								identical:
								{
									field: "register_password2"
								}
							}
						},
						register_password2:
						{
							validators:
							{
								notEmpty:
								{
								},
								identical:
								{
									field: "register_password1"
								}
							}
						},
						register_captcha:
						{
							validators:
							{
								notEmpty:
								{
								},
								stringLength:
								{
									min: 6,
									max: 6,
									message: "Veuillez fournir 6 chiffres"
								},
								regexp:
								{
									regexp: /^[0-9]+$/
								}
							}
						}
					}
				});
			});
		</script>
	</body>
</html>