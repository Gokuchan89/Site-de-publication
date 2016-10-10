<?php
	include("./class/_classLoader.php");
	
	// On ajoute une ligne dans le log
	$log = new log_activite();
	$log->setUsername($_SESSION['name']);
	$log->setAction("Déconnexion");
	$log->setComment("Déconnecté avec succès");
	$log->saveLog_activite();
	
	// On change le statut de l'utilisateur
	$user = new User();
	$user->getUserDBUsername($_SESSION['username']);
	$user->setStatus(0);
	$user->saveUser();
	
	// On détruit la session
	session_destroy();
	
	// On redirige vers la page de connexion
	header("location: ./");
?>
