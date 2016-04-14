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
	<div class="col-xs-12 col-sm-12 col-md-8 text-center">
		<!-- AFFICHE -->
		<?php $filename = sprintf('./profils/'.$menu['table'].'/affiches/Filmotech_%05d.jpg', $detail['ID']); ?>
		<?php if (file_exists($filename)) echo '<div class="detail"><div id="affiche"><a href="'.$filename.'"><img data-original="'.$filename.'" class="detail-img lazy" alt="affiche" /></a></div></div>'; else echo '<div class="detail"><img data-src="holder.js/100px100p?text=aucune \n image" alt="affiche" /></div>'; ?>
		<br/>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-4">
		<!-- DETAILS -->
		<div class="panel">
			<li class="list-group-item"><i class="fa fa-pencil"></i> <?php echo $detail['TitreVF']; ?></li>
			<?php if($detail['TitreVO'] != '') { ?><li class="list-group-item"><i class="fa fa-pencil"></i> <?php echo $detail['TitreVO']; ?></li><?php } ?>
			<li class="list-group-item"><i class="fa fa-tag"></i> <?php echo filter('genre', $detail['Genre'], $menu['id'], $menu['table']); ?></li>
			<li class="list-group-item">
				<i class="fa fa-calendar"></i> <?php echo filter('annee', $detail['Annee'], $menu['id'], $menu['table']); ?><br />
				<?php if($menu['type'] == 'livre') echo '<i class="fa fa-clock-o"></i> '.$detail['Duree'].' pages<br />'; ?>
				<?php if($menu['type'] == 'musique' || $menu['type'] == 'video') echo '<i class="fa fa-clock-o"></i> '.floor($detail['Duree']/60).'h '.($detail['Duree']%60).'min<br />'; ?>
				<?php if($menu['type'] == 'video') { ?><i class="fa fa-globe"></i> <?php echo $detail['Pays']; ?><?php } ?>
			</li>
			<?php if($menu['type'] == 'video') { ?>
				<li class="list-group-item"><i class="fa fa-thumbs-o-up"></i> <img src="./img/note<?php echo $detail['Note']; ?>.png" alt="note"/></li>
				<li class="list-group-item text-center">
					<div class="row">
						<div class="col-xs-6 col-sm-6 col-md-6">
							<?php if($detail['FilmVu'] == 'OUI') echo '<div class="btn btn-primary" disabled="disabled"><i class="fa fa-eye"></i> vu</div>'; ?>
							<?php if($detail['FilmVu'] == 'NON') echo '<div class="btn btn-danger" disabled="disabled"><i class="fa fa-eye-slash"></i> non vu</div>'; ?>
						</div>
						<?php if (!empty($detail['BAChemin']) && $detail['BAType'] = 'URL') { ?>
							<div class="col-xs-6 col-sm-6 col-md-6">
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
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8">
		<!-- AUTEUR / ARTISTE / REALISATEUR -->
		<?php if (!empty($detail['Realisateurs'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-users"></i> <?php if($menu['type'] == 'livre') echo 'Auteur(s)'; if($menu['type'] == 'musique') echo 'Artiste(s) / Groupe'; if($menu['type'] == 'video') echo 'Réalisateur(s)'; ?></h3>
				</div>
				<div class="panel-body">
					<?php echo search('Realisateurs', $detail['Realisateurs'], $menu['id'], $menu['table']); ?>
				</div>
			</div>
		<?php } ?>
		<!-- PISTE / SYNOPSIS -->
		<?php if (!empty($detail['Synopsis'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-bars"></i> Synopsis</h3>
				</div>
				<div class="panel-body"><?php echo str_replace("\r", '<br/>', $detail['Synopsis']); ?></div>
			</div>
		<?php } ?>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-4">
	</div>
</div>