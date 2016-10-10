<?php
	if (isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else $id = "";
	if (isset($_GET['op']) && preg_match("/^[a-z]*$/", $_GET['op'])) $op = $_GET['op']; else $op = "";
	if (isset($_GET['tab']) && is_numeric($_GET['tab'])) $tab = $_GET['tab']; else $tab = "1";
	if (isset($_GET['table']) && is_numeric($_GET['table'])) $table = $_GET['table']; else $table = "";
	if (isset($_GET['type']) && preg_match("/^[a-z_]*$/", $_GET['type'])) $type = $_GET['type']; else $type = "";
	
	
	
	
	
	
	if (!$op) include('./template/bootstrap/pages/home.php');
	
	
	
	
	
	if ($op == 'settings') include('./template/bootstrap/pages/admin/settings.php');
	if ($op == 'log') include('./template/bootstrap/pages/admin/log.php');
	
	
	
	
	if ($op == 'logout') include('./data/logout.php');
	
	
	if ($op && $op != 'settings' && $op != 'log' && $op != 'logout') include('./template/bootstrap/pages/home.php');
?>