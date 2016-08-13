<?php
	/*
		=================================
		DERNIERS AJOUTS
		=================================
	*/
	$home_query = $db->prepare('SELECT `id`, `name`, `table` FROM `site_menu` ORDER BY `position`');
	$home_query->execute();
?>
<?php while($home = $home_query->fetch()) { ?>
	<?php
		$lastupdate_query = $db->prepare('SELECT `ID`, `TitreVF` FROM `'.$home['table'].'` ORDER BY `ID` DESC LIMIT '.$config['lastaddMax']);
		$lastupdate_query->execute();

		$total_query = $db->prepare('SELECT COUNT(`ID`) FROM `'.$home['table'].'`');
		$total_query->execute();
		$total = $total_query->fetchColumn();
		$total_query->closeCursor();
	?>
	<div class="panel panel-default">
		<div class="panel-heading">Les derniers ajouts de la catégorie <a href="./?op=list&table=<?php echo $home['id']; ?>"><?php echo $home['name']; ?></a><div class="pull-right">(<?php echo $config['lastaddMax']; ?> sur <?php echo $total; ?>)</div></div>
		<div class="panel-body">
			<div class="row text-center">
				<?php while($lastupdate = $lastupdate_query->fetch()) { ?>
					<div class="col-xs-6 col-sm-4 col-md-2">
						<a href="./?op=detail&table=<?php echo $home['id']; ?>&id=<?php echo $lastupdate['ID']; ?>">
							<div class="thumbnail">
								<?php $filename = sprintf('./profils/'.$home['table'].'/affiches/Filmotech_%05d.jpg', $lastupdate['ID']); ?>
								<?php if (file_exists($filename)) echo '<div class="lastadd"><img data-original="'.$filename.'" class="lastadd-img lazy" alt="affiche" /></div>'; else echo '<div class="lastadd"><img data-src="holder.js/100px165?text=aucune \n image" alt="affiche" /></div>'; ?>
								<div class="text-info"><?php echo $lastupdate['TitreVF']; ?></div>
							</div>
						</a>
					</div>
				<?php } $lastupdate_query->closeCursor(); ?>
			</div>
		</div>
		<div class="panel-footer">
			<?php
				$lastUpdate = '?';
				$filename = './profils/'.$home['table'].'/update.txt';
				if (file_exists($filename))
				{
					$handle = fopen($filename, "r");
					$lastUpdate = fread($handle, filesize($filename));
					fclose($handle);
				}
				echo 'Dernière mise à jour le ', $lastUpdate;
			?>
		</div>
	</div>
<?php } $home_query->closeCursor(); ?>