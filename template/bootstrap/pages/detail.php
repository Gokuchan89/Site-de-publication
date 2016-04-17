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
		<?php if ($detail['EntreeType'] == 'BD') { ?>
			<?php if (file_exists($filename)) echo '<div class="detail detail-bd" id="affiche"><a href="'.$filename.'"><img data-original="'.$filename.'" class="detail-bd-img lazy" alt="affiche" /></a></div>'; else echo '<div class="detail detail-bd"><img data-src="holder.js/275x310?text=aucune \n image" class="detail-bd-img" alt="affiche" /></div>'; ?>
		<?php } else { ?>
			<?php if (file_exists($filename)) echo '<div class="detail" id="affiche"><a href="'.$filename.'"><img data-original="'.$filename.'" class="detail-img lazy" alt="affiche" /></a></div>'; else echo '<div class="detail"><img data-src="holder.js/100px165?text=aucune \n image" alt="affiche" /></div>'; ?>
		<?php } ?>
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
							<?php if($detail['FilmVu'] == 'OUI') echo '<div class="btn btn-primary" disabled="disabled"><i class="fa fa-eye"></i> Film vu</div>'; ?>
							<?php if($detail['FilmVu'] == 'NON') echo '<div class="btn btn-danger" disabled="disabled"><i class="fa fa-eye-slash"></i> Film non vu</div>'; ?>
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
		<!-- PISTE / SYNOPSIS -->
		<?php if (!empty($detail['Synopsis'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-bars"></i> <?php if($menu['type'] == 'livre') echo 'Description'; if($menu['type'] == 'musique') echo 'Piste(s)'; if($menu['type'] == 'video') echo 'Synopsis'; ?></h3>
				</div>
				<div class="panel-body"><?php echo str_replace("\r", '<br/>', $detail['Synopsis']); ?></div>
			</div>
		<?php } ?>
		<!-- INFORMATIONS SUPPLEMENTAIRES -->
		<?php if (!empty($detail['Bonus'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-info-circle"></i> Informations supplémentaires</h3>
				</div>
				<div class="panel-body"><?php echo str_replace("\r", '<br/>' , $detail['Bonus']); ?></div>
			</div>
		<?php } ?>
		<!-- REALISATEUR -->
		<?php if (!empty($detail['Realisateurs'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-users"></i> Réalisateur(s)</h3>
				</div>
				<div class="panel-body">
					<?php echo search('realisateurs', $detail['Realisateurs'], $menu['id'], $menu['table']); ?>
				</div>
			</div>
		<?php } ?>
		<!-- AUTEUR / ARTISTE / ACTEURS -->
		<?php if (!empty($detail['Acteurs'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-users"></i> <?php if($menu['type'] == 'livre') echo 'Auteur(s)'; if($menu['type'] == 'musique') echo 'Artiste(s) / Groupe'; if($menu['type'] == 'video') echo 'Acteurs / Actrices'; ?></h3>
				</div>
				<div class="panel-body">
					<?php echo search('acteurs', $detail['Acteurs'], $menu['id'], $menu['table']); ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-4">
		<!-- DETAILS -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-paperclip"></i> Détails</h3>
			</div>
			<div class="panel-body">
				<?php if (!empty($detail['Reference'])) { ?><i class="fa fa-barcode"></i> <strong>Code-barres :</strong> <?php echo $detail['Reference']; ?><br /><?php } ?>
				<?php if ($detail['EntreeDate'] != '0000-00-00') { ?><i class="fa fa-calendar"></i> <strong>Sortie le :</strong> <?php if ($detail['EntreeDate'] != '0000-00-00') echo date_sortie(date('d F Y', strtotime($detail['EntreeDate']))); ?><br /><?php } ?>
				<?php if (!empty($detail['Support']) && ($menu['type'] == 'musique' || $menu['type'] == 'video')) { ?><i class="fa fa-tasks"></i> <strong>Support :</strong> <?php echo filter('support', $detail['Support'], $menu['id'], $menu['table']); ?><br /><?php } ?>
				<?php if (!empty($detail['NombreSupport'])) { ?><i class="fa fa-dot-circle-o"></i> <strong>Nbre support(s) :</strong> <?php echo $detail['NombreSupport']; ?><br /><?php } ?>
				<?php if (!empty($detail['Edition'])) { ?><i class="fa fa-inbox"></i> <strong>Edition :</strong> <?php echo $detail['Edition']; ?><br /><?php } ?>
				<?php if (!empty($detail['Zone']) && $menu['type'] == 'video') { ?><i class="fa fa-flag"></i> <strong>Zone :</strong> <?php $filename = './img/zones/'.$detail['Zone'].'.png'; if(file_exists($filename)) echo '<img src="'.$filename.'" style="max-height:25px;" />'; else echo $detail['Zone']; ?><br /><?php } ?>
			</div>
		</div>
		<!-- AUDIO -->
		<?php if (!empty($detail['Audio'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-comments"></i> Audio</h3>
				</div>
				<div class="panel-body">
					<table width="100%">
						<?php
							$liste_audio = explode(', ', $detail['Audio']);
							for($i=0;$i<count($liste_audio);$i++)
							{
								preg_match('/^([a-zA-Z-]+)(\d.+) ([a-zA-ZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ]+)/i', $liste_audio[$i], $audio);
								echo '<tr height="30px">';
								echo '<td style="width:35%"><img src="./img/drapeaux/'.$audio[3].'.png" style="width:20px" alt="'.$audio[3].'" /> '.$audio[3].'</td>';
								echo '<td class="text-center"><img src="./img/audio/'.$audio[1].'.png" style="width:50px" alt="'.$audio[1].'" /></td>';
								echo '<td class="text-center" style="width:15%">'.$audio[2].'</td>';
								echo '</tr>';
							}
						?>
					</table>
				</div>
			</div>
		<?php } ?>
		<!-- SOUS-TITRES -->
		<?php if (!empty($detail['SousTitres'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-cc"></i> Sous-titres</h3>
				</div>
				<div class="panel-body">
					<?php
						$soustitres = explode(', ', $detail['SousTitres']);
						for($i=0;$i<count($soustitres);$i++)
						{
							echo '<img src="./img/drapeaux/'.$soustitres[$i].'.png" style="width:20px" alt="'.$soustitres[$i].'" /> '.$soustitres[$i].'<br />';
						}
					?>
				</div>
			</div>
		<?php } ?>
	</div>
</div>