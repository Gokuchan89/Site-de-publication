<?php
	if (!isset($_SESSION['username']))
	{
		header('location: ./');
		exit();
	}

	if (isset($_['themeButton']))
	{
		// Vérification que le fichier index.php soit présent dans le dossier
		if (!file_exists('./template/'.$_['theme'].'/index.php'))
		{
			$themeMessage = 'Ce thème "'.$_['theme'].'" ne contient pas le fichier index.php.';
			$i++;
		}
	}

	// Pas d'erreur, on modifie le thème de l'utilisateur
	if (isset($_['themeButton']) && $i == 0)
	{
		$query = $db->prepare('UPDATE `site_user` SET `theme` = :theme WHERE `id` = :id');
		$query->bindValue(':theme', $_['theme'], PDO::PARAM_STR);
		$query->bindValue(':id', $userid, PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}
?>
<script>document.title += " / Thèmes"</script>
<div class="panel panel-default">
	<div class="panel-heading">Thèmes</div>
	<form method="POST">
		<div class="panel-body">
			<?php if (isset($themeMessage)) echo '<div class="alert alert-danger">'.$themeMessage.'</div>'; ?>
			<div class="row text-center">
				<div class="radio">
					<?php
						$dirname = './template';
						$dir = opendir($dirname);
						while (false !== ($file = readdir($dir)))
						{
							if ($file != '.' && $file != '..')
							{
								if ($file == $user['theme']) $checked = 'checked'; else $checked = '';
								echo '<div class="col-xs-6 col-sm-4 col-md-2">';
								echo '<label class="avatar-theme"><input type="radio" name="theme" value="'.$file.'" '.$checked.' /><img src="./template/'.$file.'/'.$file.'.jpg" class="theme-img" title="'.ucfirst($file).'" /><div class="text-center">'.ucfirst($file).'</div></label>';
								echo '</div>';
							}
						}
						closedir($dir);
					?>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right" name="themeButton" <?php if ($user['rank'] == '1') echo 'disabled'; ?>>Modifier</button></div>
	</form>
</div>