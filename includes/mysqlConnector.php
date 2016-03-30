<?php
	$cfg = new CONSTANTS();
	try
	{
		$db = new PDO($cfg->DB_CONFIG.':host='.$cfg->DB_HOST.';dbname='.$cfg->DB_NAME, $cfg->DB_USER, $cfg->DB_PASSWORD);
		$db->query('SET NAMES UTF8');
	}
	catch (Exception $e)
	{
		die('Erreur : '.$e->getMessage());
	}
?>