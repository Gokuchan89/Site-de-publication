<?php
	require_once("../class/_classLoader.php");

	if (!empty($_POST['id']))
	{
		$id = $_POST['id'];
		
		$detail = new Detail();
		$detail->deleteDetailDBID($id);
		
		echo "success";
	}
?>