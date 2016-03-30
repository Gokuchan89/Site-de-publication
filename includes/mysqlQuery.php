<?php

	/*
		=================================
		CONFIG
		=================================
	*/
	$query = $db->query('SELECT `key`, `value` from site_configuration');
	while ($row = $query->fetch()) 
	{
		$config[$row['key']] = $row['value'];
	}

	/*
		=================================
		UTILISATEUR
		=================================
	*/
	if(isset($_SESSION['username']))
	{
		$query = $db->prepare('SELECT `id`, `username`, `avatar`, `rank`, `theme` FROM site_user WHERE `username` = :username');
		$query->bindValue('username', $_SESSION['username'], PDO::PARAM_STR);
		$query->execute();
		$user = $query->fetch();
		$query->CloseCursor();
	}

	/*
		=================================
		DATE DERNIERE VISITE
		=================================
	*/
	if(isset($_SESSION['username']))
	{
		$query = $db->prepare('UPDATE site_user SET `date_lastlogin` = :date_lastlogin WHERE `username` = :username');
		$query->bindValue(':date_lastlogin', date('Y-m-d H:i:s'), PDO::PARAM_INT);
		$query->bindValue(':username', $user['username'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();
	}
	
	/*
		=================================
		CATEGORIES
		=================================
	*/
	$category_query = $db->prepare('SELECT `id`, `name` FROM site_category');
	$category_query->execute();
	
	/*
		=================================
		PROFIL
		=================================
	*/
	if(isset($_SESSION['username']))
	{
		if (isset($_GET['userid']) && $_GET['userid'] > '0') $userid = (int) $_GET['userid']; else $userid = (int) $user['id'];
		
		$query = $db->prepare('SELECT `id`, `username`, `password`, `mail`, `date_registration`, `date_lastlogin`, `date_birthday`, `sex`, `url_website`, `url_facebook`, `url_twitter`, `url_googleplus`, `country`, `avatar`, `rank` FROM site_user WHERE `id` = :id');
		$query->bindValue(':id', $userid, PDO::PARAM_INT);
		$query->execute();
		$profile = $query->fetch();
		$query->CloseCursor();
	}
	
	/*
		=================================
		PARAMETRES -> MEMBRES
		=================================
	*/
	$settings_members_query = $db->prepare('SELECT `id`, `username`, `mail`, `avatar`, `rank`, `access` FROM site_user ORDER BY `rank` DESC, `username`');
	$settings_members_query->execute();
	
	/*
		=================================
		PARAMETRES -> MENU
		=================================
	*/
	$settings_category_query = $db->prepare('SELECT `id`, `name` FROM site_category');
	$settings_category_query->execute();
	
	/*
		=================================
		DERNIERS AJOUTS
		=================================
	*/
	$home_query = $db->prepare('SELECT `id`, `name`, `table` FROM site_menu ORDER BY `name`');
	$home_query->execute();
?>