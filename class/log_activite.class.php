<?php
	if(!class_exists('Log_activite'))
	{
		class Log_activite
		{
			// Liste des attributs
			private $mysql;
			private $id;
			private $date_time;
			private $username;
			private $module;
			private $action;
			private $comment;

			// Liste des getteurs
			public function getID()
			{
				return $this->id;
			}
			public function getDatetime()
			{
				return $this->date_time;
			}
			public function getUsername()
			{
				return $this->username;
			}
			public function getModule()
			{
				return $this->module;
			}
			public function getAction()
			{
				return $this->action;
			}
			public function getComment()
			{
				return $this->comment;
			}

			// Liste des setteurs
			public function setID($id)
			{
				$this->id = $id;
			}
			public function setDatetime($date_time)
			{
				$this->date_time = $date_time;
			}
			public function setUsername($username)
			{
				$this->username = $username;
			}
			public function setModule($module)
			{
				$this->module = $module;
			}
			public function setAction($action)
			{
				$this->action = $action;
			}
			public function setComment($comment)
			{
				$this->comment = $comment;
			}

			// Initialisation
			public function __construct(array $donnees = NULL)
			{
				$this->mysql = new MySQL();
				if (isset($donnees['id']))
				{
					$this->id = $donnees['id'];
				}
				if (isset($donnees['date_time']))
				{
					$this->date_time = $donnees['date_time'];
				}
				if (isset($donnees['username']))
				{
					$this->username = $donnees['username'];
				}
				if (isset($donnees['module']))
				{
					$this->module = $donnees['module'];
				} else {
					$this->module = "Application";
				}
				if (isset($donnees['action']))
				{
					$this->action = $donnees['action'];
				}
				if (isset($donnees['comment']))
				{
					$this->comment = $donnees['comment'];
				}
			}

			// Récuperation de la liste
			public function getLog_activiteList()
			{
				// Etablissement de la connexion à MySQL
				$Connexion = $this->mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_log_activite`");
				try
				{
					// On envoi la requête
					$sql->execute();
					$donnees = $sql->fetchAll();
					return $donnees;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Log_activite->getLog_activiteList", 
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_log_activite`"
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			// Sauvegarde d'un log_activite en BDD
			public function saveLog_activite()
			{
				// Vérifier si le log_activite existe déjà pour savoir si on ajoute le log_activite ou si on le met à jour dans la BDD
				if($this->id)
				{
					// Vérification si l'id existe dans la BDD
					// Etablissement de la connexion à MySQL
					$Connexion = $this->mysql->getPDO();
					// Préparation de la requête
					$sql = $Connexion->prepare("SELECT * FROM `site_log_activite` WHERE `id` = :id");
					try
					{
						// On envoi la requête
						$sql->execute(array("id" => $this->id));
						if ($sql->fetch(PDO::FETCH_OBJ))
						{
							// Il y a un resultat donc on maj le log_activite
							return $this->majDB();
						} else {
							// Il n'y a pas de resultat donc on créé le log_activite
							return $this->createDB();
						}
					} catch (Exception $e) {
						return "Erreur de requête : ".$e->getMessage();
					}
				}
				else
				{
					// On lance une création en BDD
					return $this->createDB();
				}
			}

			// Création de log_activite en BDD
			private function createDB()
			{
				// Etablissement de la connexion à MySQL
				$Connexion = $this->mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("INSERT INTO `site_log_activite` (`date_time`, `username`, `module`, `action`, `comment`) values (unix_timestamp(), :username, :module, :action, :comment)");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						'username' => $this->username,
						'module' => $this->module,
						'action' => $this->action,
						'comment' => $this->comment
					));
					$this->id = $Connexion->lastInsertId();
					return $this->id;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Log_activite->createDB", 
						"error" => $e->getMessage(),
						"request" => "INSERT INTO `site_log_activite` (`date_time`, `username`, `module`, `action`, `comment`) values (unix_timestamp(), ".$this->username.", ".$this->module.", ".$this->action.", ".$this->comment.")"
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			// Mise à jour de log_activite en BDD
			private function majDB()
			{
				// Etablissement de la connexion à MySQL
				$Connexion = $this->mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("UPDATE `site_log_activite` SET `date_time` = :date_time, `username` = :username, `module` = :module, `action` = :action, `comment` = :comment WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"id" => $this->id,
						"date_time" => $this->date_time,
						"username" => $this->username,
						"module" => $this->module,
						"action" => $this->action,
						"comment" => $this->comment
					));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "Log_activite->majDB", 
						"error" => $e->getMessage(),
						"request" => "UPDATE `site_log_activite` set `date_time` = ".$this->date_time.", `username` = ".$this->username.", `module` = ".$this->module.", `action` = ".$this->action.", `comment` = ".$this->comment." WHERE `id` = ".$this->id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
		}
	}
?>