<!DOCTYPE html>
<html>
	<?php include('./template/bootstrap/includes/header.php'); ?>
	<body>
		<?php include('./template/bootstrap/includes/navbar.php'); ?>
		<div class="container">
			<ol class="breadcrumb">
				<li><i class="fa fa-home"></i></li>
				<li><?php $category_name = new Category(); $category_name->getCategoryDBID($_GET['category']); echo $category_name->getName(); ?></li>
				<li>Derniers ajouts</li>
			</ol>
			
			
			
			<?php
				$menu_list = new Menu();
				$menu_list = $menu_list->getMenuDBIDCategory($_GET['category']);
			
				foreach ($menu_list as $menu => $val_menu)
				{
			?>
				<?php
					$table_total = new Menu();
					$table_total = $table_total->getNBTotalTable($val_menu['name_table']);
					
					$setting_lastaddmax = new Setting;
					$setting_lastaddmax->getSettingDBKey('lastadd_max');
				?>
				<div class="panel panel-default">
					<div class="panel-heading">Les derniers ajouts de la catégorie <a href="./?op=list&table=<?php echo $val_menu['id']; ?>"><?php echo $val_menu['name']; ?></a><?php if ($table_total != 0) echo '<div class="pull-right">('.$setting_lastaddmax->getValue().' sur '.$table_total[0]['nombre'].')</div>'; ?></div>
					<div class="panel-body">
						<div class="row">
							<?php
								$table_list = new Table();
								$table_list = $table_list->getLastupdateList($val_menu['name_table'], $setting_lastaddmax->getValue());
							?>
							<?php if ($table_list != 0) { ?>
								<?php foreach ($table_list as $table => $val_table) { ?>
									<?php
										if ($val_table['Duree'] < '60')
										{
											$duree = ($val_table['Duree']%60).'min';
										}
										elseif ($val_table['Duree'] > '60')
										{
											$duree = floor($val_table['Duree']/60).'h '.($val_table['Duree']%60).'min';
										}
									?>
									<div class="col-xs-6 col-sm-4 col-md-2">
										<a href="#" data-toggle="popover-x" data-target="#myPopover<?php echo $val_table['ID']; ?>" data-trigger="hover" data-placement="right">
											<div class="thumbnail">
												<?php $filename = sprintf("./profils/".$val_menu['name_table']."/affiches/Filmotech_%05d.jpg", $val_table['ID']); ?>
												<div class="lastadd text-center"><img src="<?php echo $filename; ?>" class="lastadd-img" alt="Affiche" /></div>
												<div class="title"><?php echo $val_table['TitreVF']; ?></div>
											</div>
										</a>
										<div id="myPopover<?php echo $val_table['ID']; ?>" class="popover popover-default popover-lg">
											<div class="arrow"></div>
											<div class="popover-title">
												<span class="close pull-right" data-dismiss="popover-x">&times;</span>
												<strong><?php echo $val_table['TitreVF']; ?></strong><br/><?php echo $val_table['Annee']; ?> | <?php echo $duree; ?> | <?php echo $val_table['Genre']; ?>
											</div>
											<div class="popover-content">
												<div class="popover-synopsis">
													<?php echo str_replace("\r", "<br/>", $val_table['Synopsis']); ?>
												</div><br/>
												<div class="popover-real-actor">
													Un film de : <?php echo str_replace("\r", " / ", $val_table['Realisateurs']); ?><br/>
													Avec : <?php echo str_replace("\r", " / ", $val_table['Acteurs']); ?>
												</div>
											</div>
										</div>
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
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			<?php
				$menu_list = new Menu();
				$menu_list = $menu_list->getMenuDBIDCategory($_GET['category']);
			
				foreach ($menu_list as $menu => $val_menu)
				{
			?>
				<?php
					$table_total = new Menu();
					$table_total = $table_total->getNBTotalTable($val_menu['name_table']);
					
					$setting_lastaddmax = new Setting;
					$setting_lastaddmax->getSettingDBKey('lastadd_max');
				?>
				<div class="panel panel-default">
					<div class="panel-heading">Les derniers ajouts de la catégorie <a href="./?op=list&table=<?php echo $val_menu['id']; ?>"><?php echo $val_menu['name']; ?></a><?php if ($table_total != 0) echo '<div class="pull-right">('.$setting_lastaddmax->getValue().' sur '.$table_total[0]['nombre'].')</div>'; ?></div>
					<div class="panel-body">
						<div class="row">
							<?php
								$table_list = new Table();
								$table_list = $table_list->getLastupdateList($val_menu['name_table'], $setting_lastaddmax->getValue());
							?>
							<?php if ($table_list != 0) { ?>
								<?php foreach ($table_list as $table => $val_table) { ?>
									<?php
										if ($val_table['Duree'] < '60')
										{
											$duree = ($val_table['Duree']%60).'min';
										}
										elseif ($val_table['Duree'] > '60')
										{
											$duree = floor($val_table['Duree']/60).'h '.($val_table['Duree']%60).'min';
										}
									?>
									<div class="col-xs-6 col-sm-4 col-md-2">
										<a href="./?op=detail&table=<?php echo $val_menu['id']; ?>&id=<?php echo $val_table['ID']; ?>">
											<div class="thumbnail" data-toggle="popover" data-style="primary" data-title="<strong><?php echo $val_table['TitreVF']; ?></strong><br/><?php echo $val_table['Annee']; ?> | <?php echo $duree; ?> | <?php echo $val_table['Genre']; ?>" data-content="<div class='popover-synopsis'><?php echo htmlspecialchars($val_table['Synopsis'], ENT_QUOTES); ?></div><br/><div class='popover-real-actor'>Un film de : <?php echo str_replace("\r", " / ", $val_table['Realisateurs']); ?><br/>Avec : <?php echo str_replace("\r", " / ", $val_table['Acteurs']); ?></div>">
												<?php $filename = sprintf("./profils/".$val_menu['name_table']."/affiches/Filmotech_%05d.jpg", $val_table['ID']); ?>
												<div class="lastadd text-center"><img src="<?php echo $filename; ?>" class="lastadd-img" alt="Affiche" /></div>
												<div class="title"><?php echo $val_table['TitreVF']; ?></div>
											</div>
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
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			<?php
				$menu_list = new Menu();
				$menu_list = $menu_list->getMenuDBIDCategory($_GET['category']);
			
				foreach ($menu_list as $menu => $val_menu)
				{
			?>
				<?php
					$table_total = new Menu();
					$table_total = $table_total->getNBTotalTable($val_menu['name_table']);
					
					$setting_lastaddmax = new Setting;
					$setting_lastaddmax->getSettingDBKey('lastadd_max');
				?>
				<div class="panel panel-default">
					<div class="panel-heading">Les derniers ajouts de la catégorie <a href="./?op=list&table=<?php echo $val_menu['id']; ?>"><?php echo $val_menu['name']; ?></a><?php if ($table_total != 0) echo '<div class="pull-right">('.$setting_lastaddmax->getValue().' sur '.$table_total[0]['nombre'].')</div>'; ?></div>
					<div class="panel-body">
						<div class="row">
							<?php
								$table_list = new Table();
								$table_list = $table_list->getLastupdateList($val_menu['name_table'], $setting_lastaddmax->getValue());
							?>
							<?php if ($table_list != 0) { ?>
								<?php foreach ($table_list as $table => $val_table) { ?>
									<?php
										if ($val_table['Duree'] < '60')
										{
											$duree = ($val_table['Duree']%60).'min';
										}
										elseif ($val_table['Duree'] > '60')
										{
											$duree = floor($val_table['Duree']/60).'h '.($val_table['Duree']%60).'min';
										}
									?>
									<div class="col-xs-6 col-sm-4 col-md-2">
										<div class="thumbnail">
											<div class="caption">
												<a href="./?op=detail&table=<?php echo $val_menu['id']; ?>&id=<?php echo $val_table['ID']; ?>"><?php echo $val_table['TitreVF']; ?></a><br/>
												<?php echo $val_table['Annee']; ?> | <?php echo $duree; ?> | <?php echo $val_table['Genre']; ?>
												<hr>
												<p><?php echo $val_table['Synopsis']; ?></p>
											</div>
											<?php $filename = sprintf("./profils/".$val_menu['name_table']."/affiches/Filmotech_%05d.jpg", $val_table['ID']); ?>
											<div class="lastadd text-center"><img src="<?php echo $filename; ?>" class="lastadd-img" alt="Affiche" /></div>
											<div class="title"><?php echo $val_table['TitreVF']; ?></div>
										</div>
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