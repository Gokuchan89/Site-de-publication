<?php
	if(!class_exists("MySQL"))
	{
		class MySQL
		{
			// Constante du serveur de BDD
			private $server;
			private $port;
			private $name;
			private $username;
			private $password;

			// Liste des getteurs
			public function getServer()
			{
				return $this->server;
			}
			public function getPort()
			{
				return $this->port;
			}
			public function getName()
			{
				return $this->name;
			}
			public function getUsername()
			{
				return $this->username;
			}
			public function getPassword()
			{
				return $this->password;
			}

			// Initialisation
			public function __construct()
			{
				@include("./data/db_config.inc.php");
				@include("../data/db_config.inc.php");
				$this->server = $db['server'];
				$this->port = $db['port'];
				$this->name = $db['name'];
				$this->username = $db['username'];
				$this->password = $db['password'];
			}

			static private $pdo = false;

			public function getPDO()
			{
				// Test si déjà connecté
				if (self::$pdo) {
					// L'objet PDO a déjà été instancié
					return self::$pdo;
				} else {
					try
					{
						$dns = "mysql:host=".$this->server.";port=".$this->port.";dbname=".$this->name;
						// Définition des options de connexion
						$options = array(
							PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", //Encodage en UTF-8
							PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //Demande à générer des erreurs en cas de problème
						);
						// Etablissement de la connexion
						self::$pdo = new PDO($dns, $this->username, $this->password, $options);
						// Renvoi l'objet pdo
						return self::$pdo;
					} catch (Exception $e) {
						return "Connexion à la base de données impossible";
					}
				}
			}

			public function requeteNoRes($sql)
			{
				$Connexion = $this->getPDO();
				return $Connexion->exec($sql);
			}

			public function requeteFichierSQL($contenu_fichier)
			{
				$array = explode(";\n", $contenu_fichier);
				$b = true;
				for ($i=0; $i < count($array) ; $i++)
				{
					$str = $array[$i];
					if ($str != "")
					{
						$str .= ";";
						$this->requeteNoRes($str);
					}
				}
				return $b;
			}

			public function testPresenceTable($nom_base, $nom_table)
			{
				// Etablissement de la connexion à MySQL
				$Connexion = $this->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = :nom_base AND TABLE_NAME = :nom_table");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"nom_base" => $nom_base,
						"nom_table" => $nom_table
					));

					// Traitement des résultats
					$donnees = $sql->fetch();
					if ($donnees[0] == $nom_table)
					{
						return true;
					} else {
						return false;
					}
				} catch (Exception $e) {
					return "Erreur de requête : ".$e->getMessage();
				}
			}
		}
	}
?>