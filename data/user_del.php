<?php
	ini_set("session.gc_maxlifetime", 28800);
	session_name("intranet");
	session_start();
	
	include("../class/_classLoader.php");

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key => &$val)
	{
		Functions::secure($val);
	}
	
	if (isset($_['id']))
	{
		$user = new User();
		$user->getUserDBID($_['id']);
		
		$user->deleteUserDBID($_['id']);
		
		$log = new Log_activite();
		$log->setUsername($_SESSION['name']);
		$log->setModule("Administration");
		$log->setAction("Utilisateurs");
		$log->setComment("L'utilisateur ".$user->getUsername()." a été supprimé par un administrateur.");
		$log->saveLog_activite();
		
		echo "success";
	}
?>