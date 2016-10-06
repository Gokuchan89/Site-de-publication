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
				'valid' => 'false',
			));
		} else {
			echo json_encode(array(
				'valid' => 'true',
			));
		}
	}

	/*
	if (isset($_POST['users_email']))
	{
		$email = $_POST['users_email'];
		
		$query = $db->prepare('SELECT * FROM `site_user` WHERE `email` = :email');
		$query->bindValue('email', $email, PDO::PARAM_STR);
		$query->execute();
		$user = $query->fetch();
		$query->CloseCursor();

		if ($user['email'] != '')
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