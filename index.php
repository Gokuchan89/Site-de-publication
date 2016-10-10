<?php
	if (!file_exists("./data/db_config.inc.php"))
	{
		header("location: ./install/step1.php");
		exit();
	} else {
		ini_set("session.gc_maxlifetime", 28800);
		session_name("intranet");
		session_start();
		
		/* Configure le script en français */
		setlocale(LC_TIME, "fr_FR", "fra");
		//Définit le décalage horaire par défaut de toutes les fonctions date/heure  
		date_default_timezone_set("Europe/Paris");
	
		include("./class/_classLoader.php");

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