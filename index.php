<?php
	session_start();

	if(!file_exists('./includes/mysqlConfig.php'))
	{
		header('location: install.php');
		exit();
	}
	else
	{
		require_once('./includes/classFunctions.php');
		require_once('./includes/mysqlConfig.php');
		require_once('./includes/mysqlConnector.php');
		require_once('./includes/mysqlQuery.php');
		
		if($config['open'] == '0')
		{
			if(!isset($_SESSION['username']))
			{
				header('location: login.php');
				exit();
			}
			else
			{
				require_once('./template/'.$user['theme'].'/index.php');
			}
		}
		else
		{
			require_once('./template/bootstrap/index.php');
		}
	}
?>