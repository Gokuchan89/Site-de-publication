<?php
	include("../class/_classLoader.php");
	
	if (isset($_POST['register_email']))
	{
		$email = $_POST['register_email'];
		
		$user = new User();
		$user->getUserDBEmail($email);

		if ($user->getEmail() != "")
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

	if (isset($_POST['user_add_email']))
	{
		$email = $_POST['user_add_email'];
		
		$user = new User();
		$user->getUserDBEmail($email);

		if ($user->getEmail() != "")
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