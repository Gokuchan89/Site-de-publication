<?php
	if(!isset($_SESSION['username']))
	{
		header('location: ./');
		exit();
	}

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach($_ as $key=>&$val)
	{
		Functions::secure($val);
	}

	if(isset($_['themeButton']))
	{
		$query = $db->prepare('UPDATE site_user SET `theme` = :theme WHERE `id` = :id');
		$query->bindValue(':theme', $_['theme'], PDO::PARAM_STR);
		$query->bindValue(':id', $userid, PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		refresh($_SERVER['REQUEST_URI']);
	}
?>
<script>document.title += ' - Thèmes'</script>	
<div class="panel panel-default">
	<div class="panel-heading">Thèmes</div>
	<form method="POST">
		<div class="panel-body">
			<div class="form-group">
				<div class="radio">
					<?php
						$dirname = './template';
						$dir = opendir($dirname);
						while(false !== ($file = readdir($dir)))
						{
							if($file != '.' && $file != '..')
							{
								if($file == $user['theme']) $checked = 'checked'; else $checked = '';
								echo '<label class="avatar-theme"><input type="radio" name="theme" value="'.$file.'" '.$checked.' /><img src="./template/'.$file.'/'.$file.'.jpg" style="width:200px;" title="'.ucfirst($file).'" /><div class="text-center">'.ucfirst($file).'</div></label>';
							}
						}
						closedir($dir);
					?>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<button type="submit" class="btn btn-success pull-right" name="themeButton" <?php if ($user['rank'] == '1') echo 'disabled'; ?>>Modifier</button>
		</div>
	</form>
</div>