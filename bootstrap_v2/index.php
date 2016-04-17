<?php
	require_once('./template/bootstrap_v2/includes/functions.php');
	
	if (isset($_GET['op']) && preg_match('/^[a-z]*$/', $_GET['op'])) $op = $_GET['op']; else $op = '';
	if (isset($_GET['table']) && preg_match('/^[0-9]*$/', $_GET['table'])) $table = $_GET['table']; else $table = '';
	if (isset($_GET['id']) && preg_match('/^[0-9]*$/', $_GET['id'])) $id = $_GET['id']; else $id = '';
	if (isset($_GET['tab']) && preg_match('/^[0-9]*$/', $_GET['tab'])) $tab = $_GET['tab']; else $tab = '1';
	
	if(!empty($_POST) OR !empty($_FILES))
	{
		$_SESSION['sauvegarde'] = $_POST ;
		$fichierActuel = $_SERVER['PHP_SELF'] ;
		if(!empty($_SERVER['QUERY_STRING']))
		{
			$fichierActuel .= '?' . $_SERVER['QUERY_STRING'] ;
		}
		header('Location: ' . $fichierActuel);
		exit;
	}
	if(isset($_SESSION['sauvegarde']))
	{
		$_POST = $_SESSION['sauvegarde'] ;
		unset($_SESSION['sauvegarde']);
	}
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
	<?php require_once('./template/bootstrap_v2/includes/header.php'); ?>
	<body>
		<?php require_once('./template/bootstrap_v2/includes/navbar.php'); ?>
		<div class="container">
			<?php
				if(isset($loginMessage[$lib_errors]))
				{
					foreach($loginMessage as $type=>$messages)
					{
						$class = 'alert ';
						$class .= $lib_errors==$type?'alert-danger':'alert-success';
						foreach ($messages as $message)
						{
							echo '<div class="'.$class.'">'.$message.'</div>';
						}
					}
				}
				
				if (!$op) require_once('./template/bootstrap_v2/pages/home.php');
				if ($op == 'list') require_once('./template/bootstrap_v2/pages/list.php');
				if ($op == 'detail') require_once('./template/bootstrap_v2/pages/detail.php');
				if ($op == 'profile') require_once('./template/bootstrap_v2/pages/profile.php');
				if ($op == 'themes') require_once('./template/bootstrap_v2/pages/themes.php');
				if ($op == 'settings') require_once('./template/bootstrap_v2/pages/settings.php');
				if ($op == 'logout') logout();
			?>
		</div>
		<?php require_once('./template/bootstrap_v2/includes/footer.php'); ?>
		<?php require_once('./template/bootstrap_v2/includes/javascript.php'); ?>
	</body>
</html>