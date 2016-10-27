<?php
	if (!file_exists("./data/db_config.inc.php"))
	{
		header("location: ./install/step1.php");
		exit();
	} else {
		session_start();
		
		/* Configure le script en français */
		setlocale(LC_TIME, "fr_FR", "fra");
		//Définit le décalage horaire par défaut de toutes les fonctions date/heure  
		date_default_timezone_set("Europe/Paris");
	
		include("./class/_classLoader.php");
		
		if (isset($_COOKIE['auth']) && !isset($_SESSION['username']))
		{
			$auth = $_COOKIE['auth'];
			$auth = explode ("------", $auth);
			
			$user = new User();
			$user->getUserDBID($auth[0]);
			
			$cryptUsername = sha1($user->getUsername());
			$cryptEmail = sha1($user->getEmail());
			$cryptDateregistration = sha1($user->getDateregistration());
						
			$cookieCrypt = sha1('q$sd196^qùs$d'.$cryptUsername.'qjjfhddkfi[{)@$'.$cryptEmail.'é!è!tyuh#^{{'.$cryptDateregistration);
			
			if ($cookieCrypt == $auth[1])
			{
				$_SESSION['name'] = $user->getName();
				$_SESSION['username'] = $user->getUsername();
				$_SESSION['admin'] = $user->getAdmin();
				
				setcookie("auth", $user->getId().'------'.$cookieCrypt, time() + 3600 * 24 * 365, "/");
			} else {
				setcookie("auth", "", time() - 3600, "/");
			}
		}

		$setting_open = new Setting();
		$setting_open->getSettingDBKey("open");
		if ($setting_open->getValue() == 0)
		{
			if (!isset($_SESSION['username'])) 
			{
				header("location: ./login.php");
				exit();
			} else {
				$user_datelastlogin = new User();
				$user_datelastlogin->getUserDBUsername($_SESSION['username']);
				$user_datelastlogin->setDatelastlogin(time());
				$user_datelastlogin->SaveUser();
				
				$user_theme = new User();
				$user_theme->getUserDBUsername($_SESSION['username']);
				include("./template/".$user_theme->getTheme()."/index.php");
			}
		}
		if ($setting_open->getValue() == 1)
		{
			if (!isset($_SESSION['username'])) 
			{
				include("./template/bootstrap/index.php");
			} else {
				$user_datelastlogin = new User();
				$user_datelastlogin->getUserDBUsername($_SESSION['username']);
				$user_datelastlogin->setDatelastlogin(time());
				$user_datelastlogin->SaveUser();
				
				$user_theme = new User();
				$user_theme->getUserDBUsername($_SESSION['username']);
				include("./template/".$user_theme->getTheme()."/index.php");
			}
		}
	}
?>