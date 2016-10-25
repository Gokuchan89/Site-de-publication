<?php
	if(!class_exists("Detail"))
	{
		class Detail
		{
			// Liste des attributs
			private $id;
			private $name;
			private $type;
			private $icon;
			private $options;
			private $id_menu;
			
			// Liste des getteurs
			public function getID()
			{
				return $this->id;
			}
			public function getName()
			{
				return $this->name;
			}
			public function getType()
			{
				return $this->type;
			}
			public function getOptions()
			{
				return $this->options;
			}
			public function getIcon()
			{
				return $this->icon;
			}
			public function getIdmenu()
			{
				return $this->id_menu;
			}

			// Liste des setteurs
			public function setName($name)
			{
				$this->name = $name;
			}
			public function setType($type)
			{
				$this->type = $type;
			}
			public function setIcon($icon)
			{
				$this->icon = $icon;
			}
			public function setOptions($options)
			{
				$this->options = $options;
			}
			public function setIdmenu($id_menu)
			{
				$this->id_menu = $id_menu;
			}

			// Initialisation
			public function __construct(array $donnees = NULL)
			{
				if (isset($donnees['id']))
				{
					$this->id = $donnees['id'];
				}
				if (isset($donnees['name']))
				{
					$this->name = $donnees['position'];
				}
				if (isset($donnees['type']))
				{
					$this->type = $donnees['type'];
				}
				if (isset($donnees['options']))
				{
					$this->options = $donnees['options'];
				}
				if (isset($donnees['icon']))
				{
					$this->icon = $donnees['icon'];
				}
				if (isset($donnees['id_menu']))
				{
					$this->id_menu = $donnees['id_menu'];
				}
			}
	
			// Initialisation de la liste via l'id
			public function getDetailDBID($id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_detail` WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array("id" => $id));
					// Traitement des résultats
					while ($menu = $sql->fetch(PDO::FETCH_OBJ))
					{
						$this->id = $menu->id;
						$this->name = $menu->name;
						$this->type = $menu->type;
						$this->icon = $menu->icon;
						$this->options = $menu->options;
						$this->id_menu = $menu->id_menu;
					}
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Detail->getDetailDBID",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_detail` WHERE `id` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			// Initialisation depuis la BDD
			public function getDetailList($type, $id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_detail` WHERE `type` = :type AND `id_menu` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"type" => $type,
						"id" => $id
					));
					// Traitement des résultats
					while ($user = $sql->fetch(PDO::FETCH_OBJ))
					{
						$this->id = $user->id;
						$this->name = $user->name;
						$this->type = $user->type;
						$this->icon = $user->icon;
						$this->options = $user->options;
						$this->id_menu = $user->id_menu;
					}
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Detail->getDetailList",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_detail` WHERE `type` = ".$type." AND `id_menu` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			// Sauvegarde d'un nouveau menu en BDD
			public function saveDetail()
			{
				// Vérifier si le menu existe déjà pour savoir si on ajoute le menu ou si on le met à jour dans la BDD
				if ($this->id)
				{
					// Vérification si l'id existe dans la BDD
					// Etablissement de la connexion à MySQL
					$mysql = new MySQL();
					$Connexion = $mysql->getPDO();
					// Préparation de la requête
					$sql = $Connexion->prepare("SELECT * FROM `site_detail` WHERE `id` = :id");
					try
					{
						// On envoi la requête
						$sql->execute(array("id" => $this->id));
						if ($sql->fetch(PDO::FETCH_OBJ))
						{
							//il y a un resultat donc on maj le menu
							return $this->majDB();
						} else {
							// il n'y a pas de resultat donc on créé le menu
							return $this->createDB();
						}
					} catch (Exception $e) {
						$Log = new Log(array(
							"treatment" => "Detail->saveDetail",
							"error" => $e->getMessage(),
							"request" => "SELECT * FROM `site_detail` WHERE `id` = ".$this->id
						));
						$Log->saveLog();
						return "Erreur de requête : ".$e->getMessage();
					}
				} else {
					// On lance une création en BDD
					return $this->createDB();
				}
			}

			private function createDB()
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("INSERT INTO `site_detail` (`name`, `type`, `icon`, `options`, `id_menu`) values (:name, :type, :icon, :options, :id_menu)");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"name" => $this->name,
						"type" => $this->type,
						"icon" => $this->icon,
						"options" => $this->options,
						"id_menu" => $this->id_menu
					));
					$this->id = $Connexion->lastInsertId();
					return $this->id;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Detail->createDB",
						"error" => $e->getMessage(),
						"request" => "INSERT INTO `site_detail` (`name`, `type`, `icon`, `options`, `id_menu`) values (".$this->name.", ".$this->type.", ".$this->icon.", ".$this->options.", ".$this->id_menu.")"
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			private function majDB()
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("UPDATE `site_detail` SET `name` = :name, `type` = :type, `icon` = :icon, `options` = :options, `id_menu` = :id_menu WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"id" => $this->id,
						"name" => $this->name,
						"type" => $this->type,
						"icon" => $this->icon,
						"options" => $this->options,
						"id_menu" => $this->id_menu
					));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Detail->majDB",
						"error" => $e->getMessage(),
						"request" => "UPDATE `site_detail` SET `name` = ".$this->name.", `type` = ".$this->type.", `icon` = ".$this->icon.", `options` = ".$this->options.", `id_menu` = ".$this->id_menu." WHERE `id` = ".$this->id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
			
			// Suppression d'un menu
			public function deleteDetailDBID($id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("DELETE FROM `site_detail` WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array("id" => $id));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Detail->deleteDetailDBID", 
						"error" => $e->getMessage(),
						"request" => "DELETE FROM `site_detail` WHERE `id` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
		}
	}
?>