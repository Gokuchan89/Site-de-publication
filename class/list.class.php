<?php
	if(!class_exists("Liste"))
	{
		class Liste
		{
			// Liste des attributs
			private $id;
			private $name;
			private $type;
			private $sort;
			private $position;
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
			public function getSort()
			{
				return $this->sort;
			}
			public function getPosition()
			{
				return $this->position;
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
			public function setSort($sort)
			{
				$this->sort = $sort;
			}
			public function setPosition($position)
			{
				$this->position = $position;
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
				if (isset($donnees['sort']))
				{
					$this->sort = $donnees['sort'];
				}
				if (isset($donnees['position']))
				{
					$this->position = $donnees['position'];
				}
				if (isset($donnees['id_menu']))
				{
					$this->id_menu = $donnees['id_menu'];
				}
			}
	
			// Initialisation de la liste via l'id
			public function getList($id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_list` WHERE `id_menu` = :id ORDER BY `position`");
				try
				{
					// On envoi la requête
					$sql->execute(array("id" => $id));
					$donnees = $sql->fetchAll();
					return $donnees;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Liste->getListDBID",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_list` WHERE `id_menu` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
	
			public function getListDBID($id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_list` WHERE `id` = :id");
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
						$this->sort = $menu->sort;
						$this->position = $menu->position;
						$this->id_menu = $menu->id_menu;
					}
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Liste->getListDBID",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_menu` WHERE `id` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
			
			public function saveListe()
			{
				// Vérifier si le menu existe déjà pour savoir si on ajoute le menu ou si on le met à jour dans la BDD
				if ($this->id)
				{
					// Vérification si l'id existe dans la BDD
					// Etablissement de la connexion à MySQL
					$mysql = new MySQL();
					$Connexion = $mysql->getPDO();
					// Préparation de la requête
					$sql = $Connexion->prepare("SELECT * FROM `site_list` WHERE `id` = :id");
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
							"treatment" => "Liste->saveListe",
							"error" => $e->getMessage(),
							"request" => "SELECT * FROM `site_list` WHERE `id` = ".$this->id
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
				$sql = $Connexion->prepare("INSERT INTO `site_list` (`name`, `type`, `sort`, `position`, `id_menu`) values (:name, :type, :sort, :position, :id_menu)");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"name" => $this->name,
						"type" => $this->type,
						"sort" => $this->sort,
						"position" => $this->position,
						"id_menu" => $this->id_menu
					));
					$this->id = $Connexion->lastInsertId();
					return $this->id;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Liste->createDB",
						"error" => $e->getMessage(),
						"request" => "INSERT INTO `site_list` (`name`, `type`, `sort`, `position`, `id_menu`) values (".$this->name.", ".$this->type.", ".$this->sort.", ".$this->position.", ".$this->id_menu.")"
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
				$sql = $Connexion->prepare("UPDATE `site_list` SET `name` = :name, `type` = :type, `sort` = :sort, `position` = :position, `id_menu` = :id_menu WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"id" => $this->id,
						"name" => $this->name,
						"type" => $this->type,
						"sort" => $this->sort,
						"position" => $this->position,
						"id_menu" => $this->id_menu
					));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Liste->majDB",
						"error" => $e->getMessage(),
						"request" => "UPDATE `site_list` SET `name` = ".$this->name.", `type` = ".$this->type.", `sort` = ".$this->sort.", `position` = ".$this->position.", `id_menu` = ".$this->id_menu." WHERE `id` = ".$this->id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
			
			public function deleteListeDBID($id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("DELETE FROM `site_list` WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array("id" => $id));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Liste->deleteListeDBID", 
						"error" => $e->getMessage(),
						"request" => "DELETE FROM `site_list` WHERE `id` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
		}
	}
?>