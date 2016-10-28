<?php
	if(!class_exists("Search"))
	{
		class Search
		{
			// Liste des attributs
			private $ID;
			private $TitreVF;
			
			// Liste des getteurs
			public function getID()
			{
				return $this->ID;
			}
			public function getTitrevf()
			{
				return $this->TitreVF;
			}

			// Initialisation
			public function __construct(array $donnees = NULL)
			{
				if (isset($donnees['ID']))
				{
					$this->ID = $donnees['ID'];
				}
				if (isset($donnees['TitreVF']))
				{
					$this->TitreVF = $donnees['TitreVF'];
				}
			}
	
			public function getSearchList($name_table, $titre_vf)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `".$name_table."` WHERE `TitreVF` LIKE \"%".$titre_vf."%\" ORDER BY `TitreVF`");
				try
				{
					// On envoi la requête
					$sql->execute();
					$donnees = $sql->fetchAll();
					return $donnees;
				} catch (Exception $e) {
					$Log=new Log(array(
						"treatment" => 'Search->getSearchList',
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `".$name_table."` WHERE `TitreVF` LIKE \"%".$titre_vf."%\""
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
			
			
			
			
			
			
			
			
			
			
			
			
		}
	}
?>