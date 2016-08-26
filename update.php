<?php
	// Création de la table site_menu_filter si elle n'existe pas
	$result = $db->query('SHOW TABLES LIKE "site_menu_filter"');
	if ($result->rowCount() == 0)
	{
		$query = $db->query('CREATE TABLE IF NOT EXISTS `site_menu_filter` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(225) NOT NULL,
			`type` varchar(225) NOT NULL,
			`sort` varchar(225) NOT NULL,
			`menu` int(11) NOT NULL,
			`position` int(11) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
		$query->closeCursor();
	}
?>