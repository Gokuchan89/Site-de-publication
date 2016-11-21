<?php
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key => &$val)
	{
		Functions::secure($val);
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
				<li><?php $category_name = new Category(); $category_name->getCategoryDBID($_['category']); echo $category_name->getName(); ?></li>
				<li>Derniers ajouts</li>
			</ol>
			<?php
				$menu_list = new Menu();
				$menu_list = $menu_list->getMenuDBIDCategory($_['category']);
			?>
			<?php foreach ($menu_list as $menu => $val_menu) { ?>
				<?php
					$table_total = new Menu();
					$table_total = $table_total->getNBTotalTable($val_menu['name_table']);
					
					$setting_lastaddmax = new Setting;
					$setting_lastaddmax->getSettingDBKey('lastadd_max');
				?>
				<div class="panel panel-default">
					<div class="panel-heading">Les derniers ajouts de la table <a href="./?op=list&category=<?php echo $_GET['category']; ?>&menu=<?php echo $val_menu['id']; ?>"><?php echo $val_menu['name']; ?></a><?php if ($table_total != 0) echo '<div class="pull-right">('.$setting_lastaddmax->getValue().' sur '.$table_total[0]['nombre'].')</div>'; ?></div>
					<div class="panel-body">
						<div class="regular">
								<?php
									$lastupdate_list = new Table();
									$lastupdate_list = $lastupdate_list->getLastupdateList($val_menu['name_table'], $setting_lastaddmax->getValue());
								?>
								<?php if ($lastupdate_list != 0) { ?>
									<?php foreach ($lastupdate_list as $table => $val_table) { ?>
										<?php
											if ($val_table['Duree'] < "60")
											{
												$duree = ($val_table['Duree']%60)."min";
											}
											elseif ($val_table['Duree'] > "60")
											{
												$duree = floor($val_table['Duree']/60)."h ".($val_table['Duree']%60)."min";
											}
										?>
											<div class="thumbnail">
												<a href="./?op=detail&category=<?php echo $_GET['category']; ?>&menu=<?php echo $val_menu['id']; ?>&id=<?php echo $val_table['ID']; ?>" style="text-decoration: none; color: black;">
													<?php $filename = sprintf("./profils/".$val_menu['name_table']."/affiches/Filmotech_%05d.jpg", $val_table['ID']); ?>
													<?php if (file_exists($filename)) echo "<div class=\"lastadd-list-detail\"><img data-lazy=\"".$filename."\" alt=\"affiche\" /></div>"; else echo "<div class=\"lastadd-list-detail\"><img data-src=\"holder.js/100px100p?text=aucune \n image\" alt=\"affiche\" /></div>"; ?>
													<div class="title"><?php echo $val_table['TitreVF']; ?></div>
												</a>
											</div>
									<?php } ?>
								<?php } ?>
						</div>
					</div>
					<div class="panel-footer">
						<?php
							$lastUpdate = '?';
							$filename = './profils/'.$val_menu['name_table'].'/update.txt';
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
			<?php } ?>
		</div>
		<?php include('./template/bootstrap/includes/footer.php'); ?>
		<?php include('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>