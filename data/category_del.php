<?php
	require_once("../class/_classLoader.php");

	if (!empty($_POST['id']))
	{
		$id = $_POST['id'];
		
		$category = new Category();
		$category->deleteCategoryDBID($id);
		
		$menu = new Menu();
		$menu->deleteMenuDBIDCategory($id);
		
		echo "success";
	}
?>