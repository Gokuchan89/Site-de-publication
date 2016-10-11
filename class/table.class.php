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
	
			// R�cuperation de la liste des cat�gories
			public function getLastupdateList($name_table, $lastadd_max)
			{
				// Etablissement de la connexion � MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Pr�paration de la requ�te
				$sql = $Connexion->prepare("SELECT * FROM `".$name_table."` ORDER BY `ID` DESC LIMIT ".$lastadd_max);
				try
				{
					// On envoi la requ�te
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
					return "Erreur de requ�te : ".$e->getMessage();
				}
			}
		}
	}
?>