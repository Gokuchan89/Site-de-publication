<?php
	/*
		=================================
		CONFIG
		=================================
	*/
	$query = $db->query('SELECT `key`, `value` from `site_configuration`');
	while ($row = $query->fetch())
	{
		$config[$row['key']] = $row['value'];
	}

	/*
		=================================
		UTILISATEUR
		=================================
	*/
	if (isset($_SESSION['username']))
	{
		$query = $db->prepare('SELECT `id`, `username`, `avatar`, `rank`, `theme` FROM `site_user` WHERE `username` = :username');
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
	if (isset($_SESSION['username']))
	{
		$query = $db->prepare('UPDATE `site_user` SET `date_lastlogin` = :date_lastlogin WHERE `username` = :username');
		$query->bindValue(':date_lastlogin', date('Y-m-d H:i:s'), PDO::PARAM_INT);
		$query->bindValue(':username', $user['username'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();
	}
?>