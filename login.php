<?php
	if (!file_exists("./data/db_config.inc.php"))
	{
		header("location: ./");
		exit();
	}
	
	ini_set("session.gc_maxlifetime", 28800);
	session_name("intranet");
	session_start();
	
	include("./class/_classLoader.php");
	
	if (isset($_SESSION['username']))
	{
		header("location: ./");
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Connexion</title>
		<!-- BOOTSTRAP 3.3.7 -->
		<link rel="stylesheet" href="./template/bootstrap/css/bootstrap.min.css">
		<!-- FONT-AWESOME 4.6.3 -->
		<link rel="stylesheet" href="./template/bootstrap/plugins/font-awesome/css/font-awesome.min.css" />
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
			
			
			
			
			
			<div class="row">
				<div class="col-md-6">
					Contenu de la SESSION
					<?php var_dump($_SESSION); ?>
				</div>
				<div class="col-md-6">
					Contenu du COOKIE
					<?php var_dump($_COOKIE); ?>
				</div>
			</div>
		
		
		
		
		
		
		
		
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
					<div class="panel panel-default">
						<div class="panel-heading">Connexion</div>
						<div class="panel-body">
							<?php
								$setting_maintenance = new Setting();
								$setting_maintenance->getSettingDBKey('maintenance');
								if ($setting_maintenance->getValue() == 1)
								{
									$setting_message_maintenance = new Setting();
									$setting_message_maintenance->getSettingDBKey('message_maintenance');
									echo "<div class=\"alert alert-warning\">".$setting_message_maintenance->getValue()."</div>";
								}
							?>
							<div id="message"></div>
							<div class="form-group has-feedback">
								<input type="text" class="form-control" id="login_username" placeholder="Identifiant" autofocus required />
								<span class="form-control-feedback"><i class="fa fa-user"></i></span>
								<span id="helpBlock" class="help-block">
									<?php
										$setting_registration = new Setting();
										$setting_registration->getSettingDBKey('registration');
										if ($setting_registration->getValue() == 1)
										{
											echo "Pas de compte ? <a href=\"./register.php\" class=\"text-center\">S'inscire</a>";
										}
									?>
								</span>
							</div>
							<div class="form-group has-feedback">
								<input type="password" class="form-control" id="login_password" placeholder="Mot de passe" autocomplete="off" required />
								<span class="form-control-feedback"><i class="fa fa-lock"></i></span>
								<!--<span id="helpBlock" class="help-block"><a href="./lost_password.php">Mot de passe oublié ?</a></span>-->
							</div>
							<div class="form-group">
								<div class="checkbox">
									<label>
										<input type="checkbox" id="login_remember" /> Se souvenir de moi
									</label>
								</div>
							</div>
						</div>
						<div class="panel-footer clearfix">
							<?php
								$setting_invite = new Setting();
								$setting_invite->getSettingDBKey('invite');
								if ($setting_invite->getValue() == 1)
								{
									echo "<button type=\"submit\" class=\"btn btn-primary\" onclick=\"validLogin2()\">Se connecter (invité)</button>";
								}
							?>
							<button type="submit" class="btn btn-success pull-right" onclick="validLogin()">Se connecter</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- JQUERY 3.1.1 -->
		<script src="./template/bootstrap/js/jquery.min.js"></script>
		<!-- BOOTSTRAP 3.3.7 -->
		<script src="./template/bootstrap/js/bootstrap.min.js"></script>
		<script>
			//Ecoute de la touche entrée pour permettre de valider directement avec le clavier                
			$(document).keyup(function(touche) // on écoute l'évènement keyup()
			{
				var appui = touche.which || touche.keyCode; // le code est compatible tous navigateurs grâce à ces deux propriétés
				if (appui == 13) // si le code de la touche est égal à 13 (Entrée)
				{
					validLogin();
				}
			});

			function validLogin()
			{
				// Values
				var username = $("#login_username").val();
				var password = $("#login_password").val();
				if(document.getElementById("login_remember").checked == true)
				{
					var remember = $("#login_remember").val();
				} else {
					var remember = "";
				}
				
				$.ajax(
				{
					url: "./data/valid_login.php",
					type: "POST",
					data:
					{
						username: username,
						password: password,
						remember: remember
					},
					success: function(response)
					{
						var result = $.trim(response);
						if (result == "success")
						{
							var message = "<div class=\"alert alert-success\"><strong>Authentification réussie !</strong><br/>Chargement de vos paramètres personnels.</div>";
							$("#message").empty();
							$("#message").html(message);
							$("body").fadeOut(2500,function()
							{
								$("body").empty();
								document.location.href = "./";
							});
						} else if (result == "vide") {
							var message = "<div class=\"alert alert-danger\"><strong>Echec de l'authentification !</strong><br/>Veuillez renseigner un identifiant et/ou un mot de passe.</div>";
							$("#message").empty();
							$("#message").html(message);
							$("#login_username").val("");
							$("#login_password").val("");
						} else if (result == "compte inactif") {
							var message = "<div class=\"alert alert-danger\"><strong>Echec de l'authentification !</strong><br/>Votre compte est provisoirement suspendu.</div>";
							$("#message").empty();
							$("#message").html(message);
							$("#login_username").val("");
							$("#login_password").val("");
						} else if (result == "maintenance") {
							var message = "<div class=\"alert alert-danger\">Impossible de se connecter pendant la maintenance.</div>";
							$("#message").empty();
							$("#message").html(message);
							$("#login_username").val("");
							$("#login_password").val("");
						} else {
							var message = "<div class=\"alert alert-danger\"><strong>Echec de l'authentification !</strong><br/>Identifiant et/ou mot de passe invalide.</div>";
							$("#message").empty();
							$("#message").html(message);
							$("#login_username").val("");
							$("#login_password").val("");
						}
					}
				})
			}
			
			<?php 
				$setting_invite = new Setting();
				$setting_invite->getSettingDBKey('invite');
				if ($setting_invite->getValue() == 1)
				{
			?>
				function validLogin2()
				{
					$.ajax(
					{
						url: "./data/valid_login.php",
						type: "POST",
						data:
						{
							username: "anonymous",
							password: "anonymous"
						},
						success: function(response)
						{
							var result = $.trim(response);
							if (result == "success")
							{
								var message = "<div class=\"alert alert-success\"><strong>Authentification réussie !</strong><br/>Chargement de vos paramètres personnels.</div>";
								$("#message").empty();
								$("#message").html(message);
								$("body").fadeOut(2500,function()
								{
									$("body").empty();
									document.location.href = "./";
								});
							} else if (result == "vide") {
								var message = "<div class=\"alert alert-danger\"><strong>Echec de l'authentification !</strong><br/>Veuillez renseigner un identifiant et/ou un mot de passe.</div>";
								$("#message").empty();
								$("#message").html(message);
								$("#login_username").val("");
								$("#login_password").val("");
							} else if (result == "compte inactif") {
								var message = "<div class=\"alert alert-danger\"><strong>Echec de l'authentification !</strong><br/>Votre compte est provisoirement suspendu.</div>";
								$("#message").empty();
								$("#message").html(message);
								$("#login_username").val("");
								$("#login_password").val("");
							} else if (result == "maintenance") {
								var message = "<div class=\"alert alert-danger\">Impossible de se connecter pendant la maintenance.</div>";
								$("#message").empty();
								$("#message").html(message);
								$("#login_username").val("");
								$("#login_password").val("");
							} else {
								var message = "<div class=\"alert alert-danger\"><strong>Echec de l'authentification !</strong><br/>Identifiant et/ou mot de passe invalide.</div>";
								$("#message").empty();
								$("#message").html(message);
								$("#login_username").val("");
								$("#login_password").val("");
							}
						}
					})  
				}
			<?php } ?>
		</script>
	</body>
</html>