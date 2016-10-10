<?php
	ini_set("session.gc_maxlifetime", 28800);
	session_name("intranet");
	session_start();

	require_once("../class/_classLoader.php");

	if (!empty($_POST['username']) && !empty($_POST['password']))
	{
		$username = $_POST['username'];
		$password = sha1($_POST['password']);
		
		$user = new User();
		$user->getUserDBUsername($username);
		
		if ($user->getUsername() == $username)
		{
			if ($user->getPassword() == $password)
			{
				if ($user->getAccess() == 1)
				{
					$_SESSION['name'] = $user->getName();
					$_SESSION['username'] = $user->getUsername();
					$_SESSION['admin'] = $user->getAdmin();

					$setting_maintenance = new Setting();
					$setting_maintenance->getSettingDBKey('maintenance');
					
					if ($setting_maintenance->getValue() == 0)
					{
						$log = new Log_activite();
						$log->setUsername($user->getName());
						$log->setAction("Connexion");
						$log->setComment("Connecté avec succès (IP : ".$_SERVER['REMOTE_ADDR'].")");
						$log->saveLog_activite();
						
						$user->setStatus(1);
						$user->saveUser();
						
						echo "success";
					} else {
						if ($_SESSION['admin'] == 1)
						{
							$log = new Log_activite();
							$log->setUsername($user->getName());
							$log->setAction("Connexion");
							$log->setComment("Connecté avec succès (IP : ".$_SERVER['REMOTE_ADDR'].")");
							$log->saveLog_activite();
							
							$user->setStatus(1);
							$user->saveUser();
							
							echo "success";
						} else {
							$log = new Log_activite();
							$log->setUsername($user->getName());
							$log->setAction("Connexion");
							$log->setComment("Connexion refusée car maintenance active (IP : ".$_SERVER['REMOTE_ADDR'].")");
							$log->saveLog_activite();
							
							session_destroy();
							
							echo "maintenance";
						}
					}
				} else {
					$log = new Log_activite();
					$log->setUsername($user->getName());
					$log->setAction("Connexion");
					$log->setComment("Connexion refusée car compte inactif (IP : ".$_SERVER['REMOTE_ADDR'].")");
					$log->saveLog_activite();
					
					echo "compte inactif";
				}
			} else {
				$log = new Log_activite();
				$log->setUsername($user->getName());
				$log->setAction("Connexion");
				$log->setComment("Connexion refusée car erreur de mot de passe (IP : ".$_SERVER['REMOTE_ADDR'].")");
				$log->saveLog_activite();
				
				echo "erreur";
			}
		} else {
			$log = new Log_activite();
			$log->setUsername($username);
			$log->setAction("Connexion");
			$log->setComment("Connexion refusée car erreur d'identifiant (IP : ".$_SERVER['REMOTE_ADDR'].")");
			$log->saveLog_activite();
			
			echo "erreur";
		}
	} else {
		$log = new Log_activite();
		$log->setUsername("");
		$log->setAction("Connexion");
		$log->setComment("Connexion refusée car identifiant et/ou mot de passe non renseigné (IP : ".$_SERVER['REMOTE_ADDR'].")");
		$log->saveLog_activite();
		
		echo "vide";
	}
?>