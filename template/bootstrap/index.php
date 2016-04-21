<?php
	require_once('./template/bootstrap/includes/functions.php');

	if (isset($_GET['op']) && preg_match('/^[a-z]*$/', $_GET['op'])) $op = $_GET['op']; else $op = '';
	if (isset($_GET['tab']) && is_numeric($_GET['tab'])) $tab = $_GET['tab']; else $tab = '1';
	if (isset($_GET['table']) && is_numeric($_GET['table'])) $table = $_GET['table']; else $table = '';
	if (isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else $id = '';

	if ($tab > '3') $tab = '1';
	$i = 0;
	
	/*
	if (!empty($_POST) OR !empty($_FILES))
	{
		$_SESSION['sauvegarde'] = $_POST ;
		$_SESSION['sauvegardeFILES'] = $_FILES ;
		
		$fichierActuel = $_SERVER['PHP_SELF'] ;
		if (!empty($_SERVER['QUERY_STRING']))
		{
			$fichierActuel .= '?' . $_SERVER['QUERY_STRING'] ;
		}
		
		header('Location: ' . $fichierActuel);
		exit;
	}

	if (isset($_SESSION['sauvegarde']))
	{
		$_POST = $_SESSION['sauvegarde'] ;
		$_FILES = $_SESSION['sauvegardeFILES'] ;
		
		unset($_SESSION['sauvegarde'], $_SESSION['sauvegardeFILES']);
	}
	*/

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key=>&$val)
	{
		Functions::secure($val);
	}
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
	<?php require_once('./template/bootstrap/includes/header.php'); ?>
	<body>
		<?php require_once('./template/bootstrap/includes/navbar.php'); ?>
		<div class="container">
			<?php
				if (isset($loginMessage)) echo '<div class="alert alert-danger">'.$loginMessage.'</div>';
				if (!$op) require_once('./template/bootstrap/pages/home.php');
				if ($op == 'list') require_once('./template/bootstrap/pages/list.php');
				if ($op == 'detail') require_once('./template/bootstrap/pages/detail.php');
				if ($op == 'profile') require_once('./template/bootstrap/pages/profile.php');
				if ($op == 'themes') require_once('./template/bootstrap/pages/themes.php');
				if ($op == 'settings') require_once('./template/bootstrap/pages/settings.php');
				if ($op == 'logout') logout();
				if ($op && $op != 'list' && $op != 'detail' && $op != 'profile' && $op != 'themes' && $op != 'settings' && $op != 'logout') require_once('./template/bootstrap/pages/home.php');
			?>
		</div>
		<?php require_once('./template/bootstrap/includes/footer.php'); ?>
		<?php require_once('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>