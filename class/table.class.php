<?php
	if (!class_exists('Table'))
	{
		class Table
		{
			// Liste des attributs
			private $ID;
			private $TitreVF;

			// Liste des getteurs
			public function getId()
			{
				return $this->ID;
			}
			public function getTitrevf()
			{
				return $this->TitreVF;
			}

			// Liste des setteurs
			public function setId($ID)
			{
				$this->ID = $ID;
			}
			public function setTitrevf($TitreVF)
			{
				$this->TitreVF = $TitreVF;
			}

			// Initialisation
			public function __construct(array $donnees = NULL)
			{
				$this->mysql = new MySQL();
				if (isset($donnees['ID']))
				{
					$this->ID = $donnees['ID'];
				}
				if (isset($donnees['getTitrevf']))
				{
					$this->getTitrevf = $donnees['getTitrevf'];
				}
			}
	
			// Récuperation de la liste des catégories
			public function getLastupdateList($name_table, $lastadd_max)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `".$name_table."` ORDER BY `ID` DESC LIMIT ".$lastadd_max);
				try
				{
					// On envoi la requête
					$sql->execute();
					$donnees = $sql->fetchAll();
					return $donnees;
				} catch (Exception $e) {
					$Log=new Log(array(
						"treatment" => 'Table->getLastupdateList',
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `".$name_table."` ORDER BY `ID` DESC LIMIT ".$lastadd_max
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
		}
	}
?>