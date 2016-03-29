<?php
	require_once('./template/bootstrap/includes/functions.php');
	
	if (isset($_GET['op']) && preg_match('/^[a-z]*$/', $_GET['op'])) $op = $_GET['op']; else $op = '';
	if (isset($_GET['id']) && preg_match('/^[0-9]*$/', $_GET['id'])) $id = $_GET['id']; else $id = '';
	if (isset($_GET['tab']) && preg_match('/^[0-9]*$/', $_GET['tab'])) $tab = $_GET['tab']; else $tab = '1';
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
				
				// if (!$op) require_once('./template/bootstrap/pages/home.php');
				// if ($op == 'list') require_once('./template/bootstrap/pages/list.php');
				if ($op == 'profile') require_once('./template/bootstrap/pages/profile.php');
				if ($op == 'themes') require_once('./template/bootstrap/pages/themes.php');
				if ($op == 'settings') require_once('./template/bootstrap/pages/settings.php');
				if ($op == 'logout') logout();
			?>
		</div>
		<?php require_once('./template/bootstrap/includes/footer.php'); ?>
		<?php require_once('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>