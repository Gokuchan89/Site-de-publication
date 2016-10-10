<?php
	include("../class/_classLoader.php");

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key=>&$val)
	{
		Functions::secure($val);
	}
	
	if (isset($_['register_username']))
	{
		
		$user = new User();
		$user->getUserDBUsername($_['register_username']);
		
		if ($user->getName() != "")
		{
			echo json_encode(array(
				"valid" => "false",
			));
		} else {
			echo json_encode(array(
				"valid" => "true",
			));
		}
	}
	
	if (isset($_['user_add_username']))
	{
		$user = new User();
		$user->getUserDBUsername($_['user_add_username']);
		
		if ($user->getName() != "")
		{
			echo json_encode(array(
				"valid" => "false",
			));
		} else {
			echo json_encode(array(
				"valid" => "true",
			));
		}
	}
?>