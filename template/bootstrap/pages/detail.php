<?php
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach($_ as $key=>&$val)
	{
		Functions::secure($val);
	}
	
	$menu_query = $db->prepare('SELECT `id`, `table`, `type` FROM site_menu WHERE `id` = "'.$table.'"');
	$menu_query->execute();
	$menu = $menu_query->fetch();
	$menu_query->closeCursor();
	
	$query = $db->prepare('SELECT * FROM '.$menu['table'].' WHERE `ID` = '.$id);
	$query->execute();
	$detail = $query->fetch();
	$query->closeCursor();
?>
<script>document.title += ' - Détail'</script>
<div class="row">
	<!-- AFFICHE -->
	<div class="col-xs-12 col-sm-12 col-md-8 text-center">
		<?php $filename = sprintf('./profils/'.$menu['table'].'/affiches/Filmotech_%05d.jpg', $detail['ID']); ?>
		<?php if (file_exists($filename)) echo '<div class="detail"><div id="affiche"><a href="'.$filename.'"><img data-original="'.$filename.'" class="detail-img lazy" alt="affiche" /></a></div></div>'; else echo '<div class="detail"><img data-src="holder.js/100px100p?text=aucune \n image" alt="affiche" /></div>'; ?>
		<br/>
	</div>
	<!-- DETAIL -->
	<div class="col-xs-12 col-sm-12 col-md-4">
		<div class="panel">
			<li class="list-group-item"><i class="fa fa-pencil"></i> <?php echo $detail['TitreVF']; ?></li>
			<?php if($detail['TitreVO'] != '') { ?><li class="list-group-item"><i class="fa fa-pencil"></i> <?php echo $detail['TitreVO']; ?></li><?php } ?>
			<li class="list-group-item"><i class="fa fa-tag"></i> <?php echo filtered('genre', $detail['Genre'], $menu['id'], $menu['table']); ?></li>
			<li class="list-group-item">
				<i class="fa fa-calendar"></i> <?php echo filtered('annee', $detail['Annee'], $menu['id'], $menu['table']); ?><br />
				<?php if($menu['type'] == 'livre') echo '<i class="fa fa-clock-o"></i> '.$detail['Duree'].' pages<br />'; ?>
				<?php if($menu['type'] == 'musique' || $menu['type'] == 'video') echo '<i class="fa fa-clock-o"></i> '.floor($detail['Duree']/60).'h '.($detail['Duree']%60).'min<br />'; ?>
				<?php if($menu['type'] == 'video') { ?><i class="fa fa-globe"></i> <?php echo $detail['Pays']; ?><?php } ?>
			</li>
			<?php if($menu['type'] == 'video') { ?>
				<li class="list-group-item"><i class="fa fa-thumbs-o-up"></i> <img src="./img/note<?php echo $detail['Note']; ?>.png" alt="note"/></li>
				<li class="list-group-item">
					<div class="row text-center">
						<div class="col-xs-6 col-lg-5">
							<?php if($detail['FilmVu'] == 'OUI') echo '<div class="btn btn-primary" disabled="disabled"><i class="fa fa-eye"></i> vu</div>'; ?>
							<?php if($detail['FilmVu'] == 'NON') echo '<div class="btn btn-danger" disabled="disabled"><i class="fa fa-eye-slash"></i> non vu</div>'; ?>
						</div>
						<?php if (!empty($detail['BAChemin']) && ($detail['BAType'] = 'URL')) { ?>
							<div class="col-xs-6 col-lg-7">
								<div id="bandeannonce">
									<a href="<?php echo $detail['BAChemin']; ?>" class="btn btn-default"><i class="fa fa-play"></i> Bande annonce</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
		</div>
	</div>
</div>