<?php
	if(!class_exists("Menu"))
	{
		class Menu
		{
			// Liste des attributs
			private $id;
			private $name;
			private $icon;
			private $position;
			private $name_table;
			private $id_category;
			
			// Liste des getteurs
			public function getID()
			{
				return $this->id;
			}
			public function getName()
			{
				return $this->name;
			}
			public function getIcon()
			{
				return $this->icon;
			}
			public function getPosition()
			{
				return $this->position;
			}
			public function getNametable()
			{
				return $this->name_table;
			}
			public function getIDcategory()
			{
				return $this->id_category;
			}

			// Liste des setteurs
			public function setName($name)
			{
				$this->name = $name;
			}
			public function setIcon($icon)
			{
				$this->icon = $icon;
			}
			public function setPosition($position)
			{
				$this->position = $position;
			}
			public function setNametable($name_table)
			{
				$this->name_table = $name_table;
			}
			public function setIDcategory($id_category)
			{
				$this->id_category = $id_category;
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
					$this->name = $donnees['name'];
				}
				if (isset($donnees['icon']))
				{
					$this->icon = $donnees['icon'];
				}
				if (isset($donnees['position']))
				{
					$this->position = $donnees['position'];
				}
				if (isset($donnees['name_table']))
				{
					$this->name_table = $donnees['name_table'];
				}
				if (isset($donnees['id_category']))
				{
					$this->id_category = $donnees['id_category'];
				}
			}
	
			// Initialisation de la liste via l'id
			public function getMenuDBID($id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_menu` WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array("id" => $id));
					// Traitement des résultats
					while ($menu = $sql->fetch(PDO::FETCH_OBJ))
					{
						$this->id = $menu->id;
						$this->name = $menu->name;
						$this->icon = $menu->icon;
						$this->position = $menu->position;
						$this->name_table = $menu->name_table;
						$this->id_category = $menu->id_category;
					}
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Menu->getMenuDBID",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_menu` WHERE `id` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
			
			// Initialisation de la liste via l'id catégorie
			public function getMenuDBIDCategory($id_category)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_menu` WHERE `id_category` = :id_category ORDER BY `position`");
				try
				{
					// On envoi la requête
					$sql->execute(array("id_category" => $id_category));
					$donnees = $sql->fetchAll();
					return $donnees;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Menu->getMenuDBIDCategory",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_menu` WHERE `id_category` = ".$id_category." ORDER BY `position`"
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
	
			// Récuperation de la liste des menus
			public function getNBTotalTable($name_table)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT COUNT(*) AS nombre FROM `".$name_table."`");
				try
				{
					// On envoi la requête
					$sql->execute();
					$donnees = $sql->fetchAll();
					return $donnees;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Menu->getNBTotalTable",
						"error" => $e->getMessage(),
						"request" => "SELECT COUNT(*) AS nombre FROM `".$name_table."`"
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			// Sauvegarde d'un nouveau menu en BDD
			public function saveMenu()
			{
				// Vérifier si le menu existe déjà pour savoir si on ajoute le menu ou si on le met à jour dans la BDD
				if ($this->id)
				{
					// Vérification si l'id existe dans la BDD
					// Etablissement de la connexion à MySQL
					$mysql = new MySQL();
					$Connexion = $mysql->getPDO();
					// Préparation de la requête
					$sql = $Connexion->prepare("SELECT * FROM `site_menu` WHERE `id` = :id");
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
							"treatment" => "Menu->saveMenu",
							"error" => $e->getMessage(),
							"request" => "SELECT * FROM `site_menu` WHERE `id` = ".$this->id
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
				$sql = $Connexion->prepare("INSERT INTO `site_menu` (`name`, `icon`, `position`, `name_table`, `id_category`) values (:name, :icon, :position, :name_table, :id_category)");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"name" => $this->name,
						"icon" => $this->icon,
						"position" => $this->position,
						"name_table" => $this->name_table,
						"id_category" => $this->id_category
					));
					$this->id = $Connexion->lastInsertId();
					return $this->id;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Menu->createDB",
						"error" => $e->getMessage(),
						"request" => "INSERT INTO `site_menu` (`name`, `icon`, `position`, `name_table`, `id_category`) values (".$this->name.", ".$this->icon.", ".$this->position.", ".$this->name_table.", ".$this->id_category.")"
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
				$sql = $Connexion->prepare("UPDATE `site_menu` SET `name` = :name, `icon` = :icon, `position` = :position, `name_table` = :name_table, `id_category` = :id_category WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"id" => $this->id,
						"name" => $this->name,
						"icon" => $this->icon,
						"position" => $this->position,
						"name_table" => $this->name_table,
						"id_category" => $this->id_category
					));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Menu->majDB",
						"error" => $e->getMessage(),
						"request" => "UPDATE `site_menu` SET `name` = ".$this->name.", `icon` = ".$this->icon.", `position` = ".$this->position.", `name_table` = ".$this->name_table.", `id_category` = ".$this->id_category." WHERE `id` = ".$this->id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
			
			// Suppression d'une catégorie
			public function deleteMenuDBID($id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("DELETE FROM `site_menu` WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array("id" => $id));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Menu->deleteMenuDBID", 
						"error" => $e->getMessage(),
						"request" => "DELETE FROM `site_menu` WHERE `id` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
			
			public function deleteMenuDBIDCategory($id_category)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("DELETE FROM `site_menu` WHERE `id_category` = :id_category");
				try
				{
					// On envoi la requête
					$sql->execute(array("id_category" => $id_category));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "User->deleteMenuDBIDCategory", 
						"error" => $e->getMessage(),
						"request" => "DELETE FROM `site_menu` WHERE `id_category` = ".$id_category
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			public function testPresenceMenu($name)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT COUNT(*) FROM `site_menu` WHERE `name` = :name");
				try
				{
					// On envoi la requête
					$sql->execute(array("name" => $name));

					// Traitement des résultats
					$donnees = $sql->fetchColumn();
					if ($donnees == 1)
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