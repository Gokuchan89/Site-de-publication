<?php
	if (!isset($_SESSION['username']) || $_SESSION['username'] == "anonymous")
	{
		header("location: ./");
		exit();
	}

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key => &$val)
	{
		Functions::secure($val);
	}

	$lib_errors = "Erreurs";
	
	if (isset($_['themeButton']) && $_['themeButton'] == 1 && empty($test[$lib_errors]))
	{
		if (file_exists('./template/'.$_['theme'].'/index.php'))
		{
			$user = new User();
			$user->getUserDBUsername($_SESSION['username']);
			$user->setTheme($_['theme']);
			$user->saveUser();
			
			header("location: ./?op=themes");
			exit();
		} else {
			$test[$lib_errors][] = "Le thème ".$_['theme']." ne contient pas le fichier index.php.";
		}
	}
?>
<!DOCTYPE html>
<html>
	<?php include('./template/bootstrap/includes/header.php'); ?>
	<body>
		<?php include('./template/bootstrap/includes/navbar.php'); ?>
		<div class="container">
			<ol class="breadcrumb">
				<li><i class="fa fa-home"></i></li>
				<li>Thèmes</li>
			</ol>
			<div class="panel panel-default">
				<div class="panel-heading">Thèmes</div>
				<form method="post" action="./?op=themes">
					<input type="hidden" name="themeButton" value="1">
					<div class="panel-body">
						<?php
							if (!empty($test[$lib_errors]))
							{
								foreach ($test as $type=>$messages)
								{
									foreach ($messages as $message)
									{
										echo "<div class=\"alert alert-danger\">".$message."</div>";
									}
								}
							}
						?>
						<div class="row text-center">
							<div class="radio">
								<?php
									$dirname = "./template";
									$dir = opendir($dirname);
									while (false !== ($file = readdir($dir)))
									{
										if ($file != "." && $file != "..")
										{
											$user = new User();
											$user->getUserDBUsername($_SESSION['username']);
											if ($file == $user->getTheme()) $checked = "checked"; else $checked = "";
											echo "<div class=\"col-xs-6 col-sm-4 col-md-2\">";
											echo "<label class=\"avatar-theme\"><input type=\"radio\" name=\"theme\" value=\"".$file."\" ".$checked." /><img src=\"./template/".$file."/".$file.".jpg\" class=\"theme-img\" title=\"".ucfirst($file)."\" /><div class=\"text-center\">".ucfirst($file)."</div></label>";
											echo "</div>";
										}
									}
									closedir($dir);
								?>
							</div>
						</div>
					</div>
					<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Modifier</button></div>
				</form>
			</div>
		</div>
		<?php include('./template/bootstrap/includes/footer.php'); ?>
		<?php include('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>