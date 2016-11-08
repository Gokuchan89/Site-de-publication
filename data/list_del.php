<?php
	require_once("../class/_classLoader.php");

	if (!empty($_POST['id']))
	{
		$id = $_POST['id'];
		
		$liste = new liste();
		$liste->deleteListeDBID($id);
		
		echo "success";
	}
?>