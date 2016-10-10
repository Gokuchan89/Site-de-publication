<?php
	if(!class_exists("Category"))
	{
		class Category
		{
			// Liste des attributs
			private $id;
			private $name;
			
			// Liste des getteurs
			public function getID()
			{
				return $this->id;
			}
			public function getName()
			{
				return $this->name;
			}

			// Liste des setteurs
			public function setName($name)
			{
				$this->name = $name;
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
			}
	
			// Initialisation de la liste
			public function getCategoryList()
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_category` ORDER BY `name`");
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
						"request" => "SELECT * FROM `site_category` ORDER BY `name`"
					));
					$Log->Save();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
	
			// Initialisation de la liste via l'id
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

			// Sauvegarde d'une catégorie
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

			// Création d'une catégorie si elle n'existe pas
			private function createDB()
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("INSERT INTO `site_category` (`name`) values (:name)");
				try
				{
					// On envoi la requête
					$sql->execute(array("name" => $this->name));
					$this->id = $Connexion->lastInsertId();
					return $this->id;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Category->createDB",
						"error" => $e->getMessage(),
						"request" => "INSERT INTO `site_category` (`name`) values (".$this->name.")"
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			// Mise à jour d'une catégorie si elle existe
			private function majDB()
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("UPDATE `site_category` SET `name` = :name WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"id" => $this->id,
						"name" => $this->name
					));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Category->majDB",
						"error" => $e->getMessage(),
						"request" => "UPDATE `site_category` SET `name` = ".$this->name." WHERE `id` = ".$this->id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
			
			// Suppression d'une catégorie
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

			// Test si la catégorie à bien été créée pendant l'installation
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