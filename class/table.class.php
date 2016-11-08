<?php
	if (!class_exists('Table'))
	{
		class Table
		{
			// Liste des attributs
			private $ID;
			private $TitreVF;
			private $TitreVO;
			private $Genre;
			private $Annee;
			private $Duree;
			private $Pays;
			private $Note;
			private $FilmVu;
			private $BAType;
			private $BAChemin;
			private $MediaType;
			private $MediaChemin;
			private $Synopsis;
			private $Realisateurs;
			private $Acteurs;
			private $Bonus;
			private $Support;
			private $Reference;
			private $Edition;
			private $EntreeDate;
			private $NombreSupport;
			private $Zone;
			private $Audio;
			private $SousTitres;

			// Liste des getteurs
			public function getId()
			{
				return $this->ID;
			}
			public function getTitrevf()
			{
				return $this->TitreVF;
			}
			public function getTitrevo()
			{
				return $this->TitreVO;
			}
			public function getGenre()
			{
				return $this->Genre;
			}
			public function getAnnee()
			{
				return $this->Annee;
			}
			public function getDuree()
			{
				return $this->Duree;
			}
			public function getPays()
			{
				return $this->Pays;
			}
			public function getNote()
			{
				return $this->Note;
			}
			public function getFilmvu()
			{
				return $this->FilmVu;
			}
			public function getBAtype()
			{
				return $this->BAType;
			}
			public function getBAchemin()
			{
				return $this->BAChemin;
			}
			public function getMediatype()
			{
				return $this->MediaType;
			}
			public function getMediachemin()
			{
				return $this->MediaChemin;
			}
			public function getSynopsis()
			{
				return $this->Synopsis;
			}
			public function getRealisateurs()
			{
				return $this->Realisateurs;
			}
			public function getActeurs()
			{
				return $this->Acteurs;
			}
			public function getBonus()
			{
				return $this->Bonus;
			}
			public function getSupport()
			{
				return $this->Support;
			}
			public function getReference()
			{
				return $this->Reference;
			}
			public function getEdition()
			{
				return $this->Edition;
			}
			public function getEntreedate()
			{
				return $this->EntreeDate;
			}
			public function getNombresupport()
			{
				return $this->NombreSupport;
			}
			public function getZone()
			{
				return $this->Zone;
			}
			public function getAudio()
			{
				return $this->Audio;
			}
			public function getSoustitres()
			{
				return $this->SousTitres;
			}

			// Initialisation
			public function __construct(array $donnees = NULL)
			{
				$this->mysql = new MySQL();
				if (isset($donnees['ID']))
				{
					$this->ID = $donnees['ID'];
				}
				if (isset($donnees['TitreVF']))
				{
					$this->TitreVF = $donnees['TitreVF'];
				}
				if (isset($donnees['TitreVO']))
				{
					$this->TitreVO = $donnees['TitreVO'];
				}
				if (isset($donnees['Genre']))
				{
					$this->Genre = $donnees['Genre'];
				}
				if (isset($donnees['Annee']))
				{
					$this->Annee = $donnees['Annee'];
				}
				if (isset($donnees['Duree']))
				{
					$this->Duree = $donnees['Duree'];
				}
				if (isset($donnees['Pays']))
				{
					$this->Pays = $donnees['Pays'];
				}
				if (isset($donnees['Note']))
				{
					$this->Note = $donnees['Note'];
				}
				if (isset($donnees['FilmVu']))
				{
					$this->FilmVu = $donnees['FilmVu'];
				}
				if (isset($donnees['BAType']))
				{
					$this->BAType = $donnees['BAType'];
				}
				if (isset($donnees['BAChemin']))
				{
					$this->BAChemin = $donnees['BAChemin'];
				}
				if (isset($donnees['MediaType']))
				{
					$this->MediaType = $donnees['MediaType'];
				}
				if (isset($donnees['MediaChemin']))
				{
					$this->MediaChemin = $donnees['MediaChemin'];
				}
				if (isset($donnees['Synopsis']))
				{
					$this->Synopsis = $donnees['Synopsis'];
				}
				if (isset($donnees['Realisateurs']))
				{
					$this->Realisateurs = $donnees['Realisateurs'];
				}
				if (isset($donnees['Acteurs']))
				{
					$this->Acteurs = $donnees['Acteurs'];
				}
				if (isset($donnees['Bonus']))
				{
					$this->Bonus = $donnees['Bonus'];
				}
				if (isset($donnees['Support']))
				{
					$this->Support = $donnees['Support'];
				}
				if (isset($donnees['Reference']))
				{
					$this->Reference = $donnees['Reference'];
				}
				if (isset($donnees['Edition']))
				{
					$this->Edition = $donnees['Edition'];
				}
				if (isset($donnees['EntreeDate']))
				{
					$this->EntreeDate = $donnees['EntreeDate'];
				}
				if (isset($donnees['NombreSupport']))
				{
					$this->NombreSupport = $donnees['NombreSupport'];
				}
				if (isset($donnees['Zone']))
				{
					$this->Zone = $donnees['Zone'];
				}
				if (isset($donnees['Audio']))
				{
					$this->Audio = $donnees['Audio'];
				}
				if (isset($donnees['SousTitres']))
				{
					$this->SousTitres = $donnees['SousTitres'];
				}
			}
	
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
					$Log = new Log(array(
						"treatment" => "Table->getLastupdateList",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `".$name_table."` ORDER BY `ID` DESC LIMIT ".$lastadd_max
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
	
			public function getFilterList($type, $name_table, $list_search)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT DISTINCT `".$type."` FROM `".$name_table."` WHERE `Sortie` = \"NON\" ".$list_search);
				try
				{
					// On envoi la requête
					$sql->execute();
					$donnees = $sql->fetchAll();
					return $donnees;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Table->getListeList",
						"error" => $e->getMessage(),
						"request" => "SELECT DISTINCT `".$type."` FROM `".$name_table."` WHERE `Sortie` = \"NON\" ".$list_search
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
	
			public function getListeList($name_table, $list_search, $option_order, $option_nb_elements, $offset_list)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `".$name_table."` WHERE `Note` >= \"0\" ".$list_search." ORDER BY ".$option_order." LIMIT ".$option_nb_elements." OFFSET ".$offset_list);
				try
				{
					// On envoi la requête
					$sql->execute();
					$donnees = $sql->fetchAll();
					return $donnees;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Table->getListeList",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `".$name_table."` WHERE `Note` >= \"0\" ".$list_search." ORDER BY ".$option_order." LIMIT ".$option_nb_elements." OFFSET ".$offset_list
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			public function getTableDBID($name_table, $id)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `".$name_table."` WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array("id" => $id));
					// Traitement des résultats
					while ($table = $sql->fetch(PDO::FETCH_OBJ))
					{
						$this->ID = $table->ID;
						$this->TitreVF = $table->TitreVF;
						$this->TitreVO = $table->TitreVO;
						$this->Genre = $table->Genre;
						$this->Annee = $table->Annee;
						$this->Duree = $table->Duree;
						$this->Pays = $table->Pays;
						$this->Note = $table->Note;
						$this->FilmVu = $table->FilmVu;
						$this->BAType = $table->BAType;
						$this->BAChemin = $table->BAChemin;
						$this->MediaType = $table->MediaType;
						$this->MediaChemin = $table->MediaChemin;
						$this->Synopsis = $table->Synopsis;
						$this->Realisateurs = $table->Realisateurs;
						$this->Acteurs = $table->Acteurs;
						$this->Bonus = $table->Bonus;
						$this->Support = $table->Support;
						$this->Reference = $table->Reference;
						$this->Edition = $table->Edition;
						$this->EntreeDate = $table->EntreeDate;
						$this->NombreSupport = $table->NombreSupport;
						$this->Zone = $table->Zone;
						$this->Audio = $table->Audio;
						$this->SousTitres = $table->SousTitres;
					}
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Table->getTableDBID",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `".$name_table."` WHERE `id` = ".$id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
	
			public function getTotal($name_table)
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
						"treatment" => "Table->getTotal",
						"error" => $e->getMessage(),
						"request" => "SELECT COUNT(*) AS nombre FROM `".$name_table."`"
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
	
			public function getSearchtotal($name_table, $list_search)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT COUNT(*) AS nombre FROM `".$name_table."` WHERE `Note` >= \"0\" ".$list_search);
				try
				{
					// On envoi la requête
					$sql->execute();
					$donnees = $sql->fetchAll();
					return $donnees;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Table->getSearchtotal",
						"error" => $e->getMessage(),
						"request" => "SELECT COUNT(*) FROM `".$name_table."` WHERE `Note` >= \"0\" ".$list_search
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
		}
	}
?>