<?php
	include("../class/_classLoader.php");
	
	if (isset($_POST['register_username']))
	{
		$username = $_POST['register_username'];
		
		$user = new User();
		$user->getUserDBUsername($username);
		
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
	
	/*
	if (isset($_POST['users_username']))
	{
		$username = $_POST['users_username'];
		
		$query = $db->prepare('SELECT * FROM `site_user` WHERE `username` = :username');
		$query->bindValue('username', $username, PDO::PARAM_STR);
		$query->execute();
		$user = $query->fetch();
		$query->CloseCursor();

		if ($user['username'] != '')
		{
			echo json_encode(array(
				'valid' => 'false',
			));
		} else {
			echo json_encode(array(
				'valid' => 'true',
			));
		}
	}
	*/
?>