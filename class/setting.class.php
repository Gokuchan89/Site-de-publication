<?php
	if(!class_exists("Setting"))
	{
		class Setting
		{
			//Liste des attributs
			private $mysql;
			private $key;
			private $value;

			//Liste des getteurs
			public function getKey()
			{
				return $this->key;
			}
			public function getValue()
			{
				return $this->value;
			}

			//Liste des setteurs
			public function setKey($key)
			{
				$this->key = $key;
			}
			public function setValue($value)
			{
				$this->value = $value;
			}

			// Initialisation
			public function __construct(array $donnees = NULL)
			{
				$this->mysql = new MySQL();
				if (isset($donnees['key']))
				{
					$this->key = $donnees['key'];
				}
				if (isset($donnees['value']))
				{
					$this->value = $donnees['value'];
				}
			}

			// Initialisation depuis la BDD via la clé
			public function getSettingDBKey($key)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_setting` WHERE `key` = :key");
				try
				{
					// On envoi la requête
					$sql->execute(array("key" => $key));
					// Traitement des résultats
					while ($setting = $sql->fetch(PDO::FETCH_OBJ))
					{
						$this->key = $setting->key;
						$this->value = $setting->value;
					}
					return true;
				} catch(Exception $e) {
					$Log = new Log(array(
						"treatment" => "Setting->getSettingDBKey",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_setting` WHERE `key` = ".$key
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			// Sauvegarde d'un Settings en BDD
			public function saveSetting()
			{
				// Vérifier si le Settings existe déjà pour savoir si on ajoute le Settings ou si on le met à jour dans la BDD
				if ($this->key)
				{
					// Vérification si l'id existe dans la BDD
					// Etablissement de la connexion à MySQL
					$mysql = new MySQL();
					$Connexion = $mysql->getPDO();
					// Préparation de la requête
					$sql = $Connexion->prepare("SELECT * FROM `site_setting` WHERE `key` = :key");
					try
					{
						// On envoi la requête
						$sql->execute(array("key" => $this->key));
						if ($sql->fetch(PDO::FETCH_OBJ))
						{
							//il y a un resultat donc on maj le Settings
							return $this->majDB();
						} else {
							// il n'y a pas de resultat donc on créé le Settings
							return $this->createDB();
						}
					} catch (Exception $e) {
						return "Erreur de requête : ".$e->getMessage();
					}
				} else {
					// On lance une création en BDD
					return $this->createDB();
				}
			}

			// Création de Settings en BDD
			private function createDB()
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("INSERT INTO `site_setting` (`value`) values (:value)");
				try
				{
					// On envoi la requête
					$sql->execute(array("value" => $this->value));
					$this->id = $Connexion->lastInsertId();
					return $this->id;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Setting->createDB", 
						"error" => $e->getMessage(),
						"request" => "INSERT INTO `site_setting` (`value`) values (".$this->value.")"
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			// Mise à jour de Settings en BDD
			private function majDB()
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("UPDATE `site_setting` SET `value` = :value WHERE `key` = :key");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"key" => $this->key,
						"value" => $this->value
					));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Setting->majDB", 
						"error" => $e->getMessage(),
						"request" => "UPDATE `site_setting` SET `value` = ".$this->value." WHERE `key` = ".$this->key
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
		}
	}
?>