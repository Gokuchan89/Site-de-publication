<?php
	if(!class_exists("Category"))
	{
		class Category
		{
			// Liste des attributs
			private $id;
			private $name;
			private $position;
			
			// Liste des getteurs
			public function getID()
			{
				return $this->id;
			}
			public function getName()
			{
				return $this->name;
			}
			public function getPosition()
			{
				return $this->position;
			}

			// Liste des setteurs
			public function setName($name)
			{
				$this->name = $name;
			}
			public function setPosition($position)
			{
				$this->position = $position;
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
				if (isset($donnees['position']))
				{
					$this->position = $donnees['position'];
				}
			}

			public function getCategoryList()
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_category` ORDER BY `position`");
				try
				{
					// On envoi la requête
					$sql->execute();
					$donnees = $sql->fetchAll();
					return $donnees;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Category->getCategoryList",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_category` ORDER BY `position`"
					));
					$Log->Save();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
	
			public function getCategoryDBID($id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_category` WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array("id" => $id));
					// Traitement des résultats
					while ($category = $sql->fetch(PDO::FETCH_OBJ))
					{
						$this->id = $category->id;
						$this->name = $category->name;
						$this->position = $category->position;
					}
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Category->getCategoryDBID",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_category` WHERE `id` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			public function saveCategory()
			{
				// Vérifier si la catégorie existe déjà pour savoir si on ajoute la catégorie ou si on le met à jour dans la BDD
				if ($this->id)
				{
					// Vérification si l'id existe dans la BDD
					// Etablissement de la connexion à MySQL
					$mysql = new MySQL();
					$Connexion = $mysql->getPDO();
					// Préparation de la requête
					$sql = $Connexion->prepare("SELECT * FROM `site_category` WHERE `id` = :id");
					try
					{
						// On envoi la requête
						$sql->execute(array("id" => $this->id));
						if ($sql->fetch(PDO::FETCH_OBJ))
						{
							//il y a un resultat donc on maj la catégorie
							return $this->majDB();
						} else {
							// il n'y a pas de resultat donc on créé la catégorie
							return $this->createDB();
						}
					} catch (Exception $e) {
						$Log = new Log(array(
							"treatment" => "Category->saveCategory",
							"error" => $e->getMessage(),
							"request" => "SELECT * FROM `site_category` WHERE `id` = ".$this->id
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
				$sql = $Connexion->prepare("INSERT INTO `site_category` (`name`, `position`) values (:name, :position)");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"name" => $this->name,
						"position" => $this->position
					));
					$this->id = $Connexion->lastInsertId();
					return $this->id;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Category->createDB",
						"error" => $e->getMessage(),
						"request" => "INSERT INTO `site_category` (`name`, `position`) values (".$this->name.", ".$this->position.")"
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
				$sql = $Connexion->prepare("UPDATE `site_category` SET `name` = :name, `position` = :position WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"id" => $this->id,
						"name" => $this->name,
						"position" => $this->position
					));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Category->majDB",
						"error" => $e->getMessage(),
						"request" => "UPDATE `site_category` SET `name` = ".$this->name.", `position` = ".$this->position." WHERE `id` = ".$this->id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
			
			public function deleteCategoryDBID($id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("DELETE FROM `site_category` WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array("id" => $id));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Category->deleteCategoryDBID", 
						"error" => $e->getMessage(),
						"request" => "DELETE FROM `site_category` WHERE `id` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			public function testPresenceCategory($name)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT COUNT(*) FROM `site_category` WHERE `name` = :name");
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