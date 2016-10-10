<?php
	require_once("../class/_classLoader.php");

	if (!empty($_POST['id']))
	{
		$id = $_POST['id'];
		
		$menu = new Menu();
		$menu->deleteMenuDBID($id);
		
		echo "success";
	}
?>