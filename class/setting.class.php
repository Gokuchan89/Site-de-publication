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

			// Initialisation depuis la BDD via le nom
			public function getSettingDBKey($key)
			{
				// Etablissement de la connexion à MySQL
				$Connexion = $this->mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_setting` WHERE `key` = :key");
				try
				{
					// On envoi la requête
					$sql->execute(array("key" => $key));
					// Traitement des résultats
					while ($user = $sql->fetch(PDO::FETCH_OBJ))
					{
						$this->key = $user->key;
						$this->value = $user->value;
					}
					return true;
				} catch(Exception $e) {
					$Log = new Log(array(
						"treatment" => "Setting->getSettingDBKey",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_setting` WHERE `key` = ".$key
					));
					$Log->Save();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
		}
	}
?>