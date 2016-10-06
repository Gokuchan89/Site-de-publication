<?php
	if(!class_exists("User"))
	{
		class User
		{
			// Liste des attributs
			private $id;
			private $name;
			private $username;
			private $password;
			private $email;
			private $date_registration;
			private $date_lastlogin;
			private $date_birthday;
			private $url_website;
			private $url_facebook;
			private $url_twitter;
			private $url_googleplus;
			private $country;
			private $avatar;
			private $theme;
			private $status;
			private $admin;
			private $access;

			// Liste des getteurs
			public function getID()
			{
				return $this->id;
			}
			public function getName()
			{
				return $this->name;
			}
			public function getUsername()
			{
				return $this->username;
			}
			public function getPassword()
			{
				return $this->password;
			}
			public function getEmail()
			{
				return $this->email;
			}
			public function getDateregistration()
			{
				return $this->date_registration;
			}
			public function getDatelastlogin()
			{
				return $this->date_lastlogin;
			}
			public function getDatebirthday()
			{
				return $this->date_birthday;
			}
			public function getUrlwebsite()
			{
				return $this->url_website;
			}
			public function getCountry()
			{
				return $this->country;
			}
			public function getAvatar()
			{
				return $this->avatar;
			}
			public function getTheme()
			{
				return $this->theme;
			}
			public function getStatus()
			{
				return $this->status;
			}
			public function getAdmin()
			{
				return $this->admin;
			}
			public function getAccess()
			{
				return $this->access;
			}

			// Liste des setteurs
			public function setName($name)
			{
				$this->name = $name;
			}
			public function setUsername($username)
			{
				$this->username = $username;
			}
			public function setPassword($password)
			{
				$this->password = sha1($password);
			}
			public function setEmail($email)
			{
				$this->email = $email;
			}
			public function setDateregistration($date_registration)
			{
				$this->date_registration = $date_registration;
			}
			public function setDatelastlogin($date_lastlogin)
			{
				$this->date_lastlogin = $date_lastlogin;
			}
			public function setDatebirthday($date_birthday)
			{
				$this->date_birthday = $date_birthday;
			}
			public function setUrlwebsite($url_website)
			{
				$this->url_website = $url_website;
			}
			public function setCountry($country)
			{
				$this->country = $country;
			}
			public function setAvatar($avatar)
			{
				$this->avatar = $avatar;
			}
			public function setTheme($theme)
			{
				$this->theme = $theme;
			}
			public function setStatus($status)
			{
				$this->status = $status;
			}
			public function setAdmin($admin)
			{
				$this->admin = $admin;
			}
			public function setAccess($access)
			{
				$this->access = $access;
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
				if (isset($donnees['username']))
				{
					$this->username = $donnees['username'];
				}
				if (isset($donnees['password']))
				{
					$this->password = sha1($donnees['password']);
				}
				if (isset($donnees['email']))
				{
					$this->email = $donnees['email'];
				}
				if (isset($donnees['date_registration']))
				{
					$this->date_registration = $donnees['date_registration'];
				}
				if (isset($donnees['date_lastlogin']))
				{
					$this->date_lastlogin = $donnees['date_lastlogin'];
				}
				if (isset($donnees['date_birthday']))
				{
					$this->date_birthday = $donnees['date_birthday'];
				}
				if (isset($donnees['url_website']))
				{
					$this->url_website = $donnees['url_website'];
				}
				if (isset($donnees['country']))
				{
					$this->country = $donnees['country'];
				}
				if (isset($donnees['avatar']))
				{
					$this->avatar = $donnees['avatar'];
				}
				if (isset($donnees['theme']))
				{
					$this->theme = $donnees['theme'];
				}
				if (isset($donnees['status']))
				{
					$this->status = $donnees['status'];
				}
				if (isset($donnees['admin']))
				{
					$this->admin = $donnees['admin'];
				}
				if (isset($donnees['access']))
				{
					$this->access = $donnees['access'];
				}
			}

			// Initialisation depuis la BDD via le username
			public function getUserDBUsername($username)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_user` WHERE `username` = :username");
				try
				{
					// On envoi la requête
					$sql->execute(array("username" => $username));

					// Traitement des résultats
					while ($user = $sql->fetch(PDO::FETCH_OBJ))
					{
						$this->id = $user->id;
						$this->name = $user->name;
						$this->username = $user->username;
						$this->email = $user->email;
						$this->password = $user->password;
						$this->date_registration = $user->date_registration;
						$this->date_lastlogin = $user->date_lastlogin;
						$this->date_birthday = $user->date_birthday;
						$this->url_website = $user->url_website;
						$this->country = $user->country;
						$this->avatar = $user->avatar;
						$this->theme = $user->theme;
						$this->status = $user->status;
						$this->admin = $user->admin;
						$this->access = $user->access;
					}
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "User->getUserDBUsername",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_user` WHERE `username` = ".$username
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}
			
			// Initialisation depuis la BDD via l'email
			public function getUserDBEmail($email)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT * FROM `site_user` WHERE `email` = :email");
				try
				{
					// On envoi la requête
					$sql->execute(array('email'=>$email));

					// Traitement des résultats
					while ($user = $sql->fetch(PDO::FETCH_OBJ))
					{
						$this->id = $user->id;
						$this->name = $user->name;
						$this->username = $user->username;
						$this->email = $user->email;
						$this->password = $user->password;
						$this->date_registration = $user->date_registration;
						$this->date_lastlogin = $user->date_lastlogin;
						$this->date_birthday = $user->date_birthday;
						$this->url_website = $user->url_website;
						$this->country = $user->country;
						$this->avatar = $user->avatar;
						$this->theme = $user->theme;
						$this->status = $user->status;
						$this->admin = $user->admin;
						$this->access = $user->access;
					}
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "User->getUserDBEmail",
						"error" => $e->getMessage(),
						"request" => "SELECT * FROM `site_user` WHERE `email` = ".$email
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			// Sauvegarde d'un nouveau User en BDD
			public function saveUser()
			{
				// Vérifier si le User existe déjà pour savoir si on ajoute le user ou si on le met à jour dans la BDD
				if ($this->id)
				{
					// Vérification si l'id existe dans la BDD
					// Etablissement de la connexion à MySQL
					$mysql = new MySQL();
					$Connexion = $mysql->getPDO();
					// Préparation de la requête
					$sql = $Connexion->prepare("SELECT * FROM `site_user` WHERE `id` = :id");
					try
					{
						// On envoi la requête
						$sql->execute(array("id" => $this->id));
						if ($sql->fetch(PDO::FETCH_OBJ))
						{
							//il y a un resultat donc on maj le user
							return $this->majDB();
						} else {
							// il n'y a pas de resultat donc on créé le user
							return $this->createDB();
						}
					} catch (Exception $e) {
						$Log = new Log(array(
							"treatment" => "User->saveUser",
							"error" => $e->getMessage(),
							"request" => "SELECT * FROM `site_user` WHERE `id` = ".$this->id
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
				$sql = $Connexion->prepare("INSERT INTO `site_user` (`name`, `username`, `password`, `email`, `date_registration`, `admin`, `access`) values (:name, :username, :password, :email, :date_registration, :admin, :access)");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"name" => $this->name,
						"username" => $this->username,
						"password" => $this->password,
						"email" => $this->email,
						"date_registration" => $this->date_registration,
						"admin" => $this->admin,
						"access" => $this->access
					));
					$this->id = $Connexion->lastInsertId();
					return $this->id;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "User->createDB",
						"error" => $e->getMessage(),
						"request" => "INSERT INTO `site_user` (`name`, `username`, `password`, `email`, `date_registration`, `admin`, `access`) values (".$this->name.", ".$this->username.", ".$this->password.", ".$this->email.", ".$this->date_registration.", ".$this->admin.", ".$this->access.")"
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
				$sql = $Connexion->prepare("UPDATE `site_user` SET `name` = :name, `username` = :username, `email` = :email, `password` = :password, `date_registration` = :date_registration, `date_lastlogin` = :date_lastlogin, `date_birthday` = :date_birthday, `url_website` = :url_website, `country` = :country, `avatar` = :avatar, `theme` = :theme, `status` = :status, `admin` = :admin, `access` = :access WHERE `id` = :id");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"id" => $this->id,
						"name" => $this->name,
						"username" => $this->username,
						"email" => $this->email,
						"password" => $this->password,
						"date_registration" => $this->date_registration,
						"date_lastlogin" => $this->date_lastlogin,
						"date_birthday" => $this->date_birthday,
						"url_website" => $this->url_website,
						"country" => $this->country,
						"avatar" => $this->avatar,
						"theme" => $this->theme,
						"status" => $this->status,
						"admin" => $this->admin,
						"access" => $this->access
					));
					return true;
				} catch (Exception $e) {
					$Log = new Log(array(
						"treatment" => "User->majDB",
						"error" => $e->getMessage(),
						"request" => "UPDATE `site_user` SET `name` = ".$this->name.", `username` = ".$this->username.", `email` = ".$this->email.", `password` = ".$this->password.", `date_registration` = ".$this->date_registration.", `date_lastlogin` = ".$this->date_lastlogin.", `date_birthday` = ".$this->date_birthday.", `url_website` = ".$this->url_website.", `country` = ".$this->country.", `avatar` = ".$this->avatar.", `theme` = ".$this->theme.", `status` = ".$this->status.", `admin` = ".$this->admin.", `access` = ".$this->access." WHERE `id` = ".$this->id
					));
					$Log->saveLog();
					return "Erreur de requête : ".$e->getMessage();
				}
			}

			public function testPresenceUser($username)
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("SELECT COUNT(*) FROM `site_user` WHERE `username` = :username");
				try
				{
					// On envoi la requête
					$sql->execute(array("username" => $username));

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