<?php
	include("../class/_classLoader.php");

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key => &$val)
	{
		Functions::secure($val);
	}

	if (isset($_['id']) && isset($_['tab']))
	{
		if ($_['tab'] == 1)
		{
			$user = new User();
			$user->getUserDBID($_['id']);
			$user->setAccess(0);
			$user->SaveUser();
			echo "success";
		}
		if ($_['tab'] == 2)
		{
			$user = new User();
			$user->getUserDBID($_['id']);
			$user->setAccess(1);
			$user->SaveUser();
			echo "success";
		}
	}
?>