<?php
	if(!class_exists("Functions"))
	{
		class Functions
		{
			public static function secure($var, $level = 1)
			{
				$var = htmlspecialchars($var, ENT_QUOTES, "UTF-8");
				if ($level < 1) $var = mysqli_real_escape_string($var);
				if ($level < 2) $var = addslashes($var);
				return $var;
			}

			public static function testDb($server, $port, $name, $username, $password)
			{
				try
				{
					$dns = "mysql:host=".$server.";port=".$port.";dbname=".$name;
					// Définition des options de connexion
					$options = array(
						PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", //Encodage en UTF-8
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //Demande à générer des erreurs en cas de problème
					);
					//Etablissement de la connexion
					new PDO($dns, $username, $password, $options);
					return true;
				} catch (Exception $e) {
					return false;
				}
			}
		}
	}
?>