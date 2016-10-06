<?php
	if(!class_exists("Log"))
	{
		class Log
		{
			// Liste des attributs
			private $date_time;
			private $treatment;
			private $error;
			private $request;

			public function __construct($donnees)
			{
				$this->date_time = time();
				if ($donnees['treatment'])
				{
					$this->treatment = $donnees['treatment'];
				} else {
					$this->treatment = "";
				}
				if ($donnees['error'])
				{
					$this->error = $donnees['error'];
				} else {
					$this->error = "";
				}
				if ($donnees['request'])
				{
					$this->request = $donnees['request'];
				} else {
					$this->request = "";
				}
			}

			public function saveLog()
			{
				// Etablissement de la connexion à MySQL
				$mysql = new MySQL();
				$Connexion = $mysql->getPDO();
				// Préparation de la requête
				$sql = $Connexion->prepare("INSERT INTO `site_log` (`date_time`, `treatment`, `error`, `request`) values (:date_time, :treatment, :error, :request)");
				try
				{
					// On envoi la requête
					$sql->execute(array(
						"date_time" => $this->date_time,
						"treatment" => $this->treatment,
						"error" => $this->error,
						"request" => $this->request
					));
					$this->id = $Connexion->lastInsertId();
					return $this->id;
				} catch (Exception $e) {
					return "Erreur de requête : ".$e->getMessage();
				}
			}
		}
	}
?>