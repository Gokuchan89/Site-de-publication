<?php
	$menu_query = $db->prepare('SELECT `id`, `name`, `table`, `type` FROM `site_menu` WHERE `id` = :id');
	$menu_query->bindValue(':id', $table, PDO::PARAM_INT);
	$menu_query->execute();
	$menu = $menu_query->fetch();
	$menu_query->closeCursor();

	$query = $db->prepare('SELECT * FROM `'.$menu['table'].'` WHERE `ID` = :id');
	$query->bindValue(':id', $id, PDO::PARAM_INT);
	$query->execute();
	$detail = $query->fetch();
	$query->closeCursor();
	
	$total = array('genre', 'annee', 'support', 'acteurs', 'realisateurs');
	for ($i=0;$i<count($total);$i++)
	{
		$query = $db->prepare('SELECT COUNT(`ID`) FROM `site_menu_filter` WHERE `menu` = "'.$table.'" AND `type` = "'.$total[$i].'"');
		$query->execute();
		$$total[$i] = $query->fetchColumn();
		$query->closeCursor();
	}
?>
<script>document.title += " / <?php echo $menu['name']; ?> / <?php echo $detail['TitreVF']; ?>"</script>

<div class="row">
	<?php if ($id != $detail['ID']) { ?>
		<div class="col-xs-12 col-sm-12 col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Information</div>
				<div class="panel-body">Désolé, mais cette fiche n'existe pas.</div>
			</div>
		</div>
		</div></div><?php require_once('./template/bootstrap/includes/footer.php'); ?><?php require_once('./template/bootstrap/includes/javascript.php'); ?></body></html>
	<?php exit(); } ?>
	<div class="col-xs-12 col-sm-12 col-md-8 text-center">
		<!-- AFFICHE -->
		<?php $filename = sprintf('./profils/'.$menu['table'].'/affiches/Filmotech_%05d.jpg', $detail['ID']); ?>
		<?php if (file_exists($filename)) echo '<div class="detail" id="affiche"><a href="'.$filename.'"><img data-original="'.$filename.'" class="detail-img lazy" alt="'.$detail['TitreVF'].'" /></a></div>'; else echo '<div class="detail"><img data-src="holder.js/100px100p?text=aucune \n image" alt="'.$detail['TitreVF'].'" /></div>'; ?>
		<br />
	</div>
	<div class="col-xs-12 col-sm-12 col-md-4">
		<!-- DETAILS -->
		<div class="panel">
			<li class="list-group-item"><i class="fa fa-pencil"></i> <?php echo $detail['TitreVF']; ?></li>
			<?php if (!empty($detail['TitreVO'])) { ?><li class="list-group-item"><i class="fa fa-pencil"></i> <?php echo $detail['TitreVO']; ?></li><?php } ?>
			<li class="list-group-item"><i class="fa fa-tag"></i> <?php echo filter('genre', $detail['Genre'], $menu['id'], $menu['table'], $genre); ?></li>
			<li class="list-group-item">
				<i class="fa fa-calendar"></i> <?php echo filter('annee', $detail['Annee'], $menu['id'], $menu['table'], $annee); ?><br />
				<?php
					if ($detail['Duree'] != '0')
					{
						if ($menu['type'] == 'livre')
						{
							echo '<i class="fa fa-book"></i> '.$detail['Duree'].' pages<br />';
						}
						if ($menu['type'] == 'musique' || $menu['type'] == 'video')
						{
							if ($detail['Duree'] < '60')
							{
								$duree = ($detail['Duree']%60).'min';
							}
							elseif ($detail['Duree'] > '60')
							{
								$duree = floor($detail['Duree']/60).'h '.($detail['Duree']%60).'min';
							}
							echo '<i class="fa fa-clock-o"></i> '.$duree.'<br />';
						}
					}
				?>
				<?php if ($menu['type'] == 'video') { ?><i class="fa fa-globe"></i> <?php echo $detail['Pays']; ?><?php } ?>
			</li>
			<?php if ($menu['type'] == 'video') { ?>
				<li class="list-group-item"><i class="fa fa-thumbs-o-up"></i> <?php if (file_exists('./img/stars/'.$detail['Note'].'.png')) echo '<img src="./img/stars/'.$detail['Note'].'.png" alt="note"/>'; else echo $detail['Note']; ?></li>
				<li class="list-group-item text-center">
					<div class="row">
						<div class="col-xs-6 col-sm-6 col-md-6">
							<?php if ($detail['FilmVu'] == 'OUI') echo '<div class="btn btn-primary" disabled="disabled"><i class="fa fa-eye"></i> Film vu</div>'; ?>
							<?php if ($detail['FilmVu'] == 'NON') echo '<div class="btn btn-danger" disabled="disabled"><i class="fa fa-eye-slash"></i> Film non vu</div>'; ?>
						</div>
						<?php if (!empty($detail['BAChemin']) && $detail['BAType'] == 'URL') { ?>
							<div class="col-xs-6 col-sm-6 col-md-6"><div id="bandeannonce"><a href="<?php echo $detail['BAChemin']; ?>" class="btn btn-default"><i class="fa fa-play"></i> Bande-annonce</a></div></div>
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
				<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-bars"></i> <?php if($menu['type'] == 'jeuxvideo' || $menu['type'] == 'livre') echo 'Description'; if($menu['type'] == 'musique') echo 'Piste(s)'; if($menu['type'] == 'video') echo 'Synopsis'; ?></h3></div>
				<div class="panel-body"><?php echo str_replace("\r", '<br/>', $detail['Synopsis']); ?></div>
			</div>
		<?php } ?>
		<!-- AUTEUR / ARTISTE / REALISATEUR -->
		<?php if (!empty($detail['Realisateurs'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-users"></i> <?php if ($menu['type'] == 'jeuxvideo') echo 'Editeur(s) / Développeur(s)'; if ($menu['type'] == 'livre') echo 'Auteur(s)'; if ($menu['type'] == 'musique') echo 'Artiste(s) / Groupe'; if ($menu['type'] == 'video') echo 'Réalisateurs'; ?></h3></div>
				<ul class="slider_detail"><?php echo search('realisateurs', $detail['Realisateurs'], $menu['id'], $menu['table'], $realisateurs); ?></ul>
			</div>
		<?php } ?>
		<!-- ACTEURS -->
		<?php if (!empty($detail['Acteurs'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-users"></i> Acteurs / Actrices</h3></div>
				<ul class="slider_detail"><?php echo search('acteurs', $detail['Acteurs'], $menu['id'], $menu['table'], $acteurs); ?></ul>
			</div>
		<?php } ?>
		<!-- INFORMATIONS SUPPLEMENTAIRES -->
		<?php if (!empty($detail['Bonus'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-info-circle"></i> Informations supplémentaires</h3></div>
				<div class="panel-body"><?php echo str_replace("\r", '<br/>' , $detail['Bonus']); ?></div>
			</div>
		<?php } ?>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-4">
		<!-- DETAILS -->
		<div class="panel panel-default">
			<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-paperclip"></i> Détails</h3></div>
			<div class="panel-body">
				<?php if (!empty($detail['Support']) && ($menu['type'] == 'jeuxvideo' || $menu['type'] == 'musique' || $menu['type'] == 'video')) { ?><i class="fa fa-tasks"></i> <strong>Support :</strong> <?php echo filter('support', $detail['Support'], $menu['id'], $menu['table'], $support); ?><br /><?php } ?>
				<?php if (!empty($detail['Edition'])) { ?><i class="fa fa-inbox"></i> <strong>Edition :</strong> <?php echo $detail['Edition']; ?><br /><?php } ?>
				<?php if (!empty($detail['Reference'])) { ?><i class="fa fa-barcode"></i> <strong>Code-barres :</strong> <?php echo $detail['Reference']; ?><br /><?php } ?>
				<?php if (!empty($detail['EntreeDate'])) { ?><i class="fa fa-calendar"></i> <strong>Sortie le :</strong> <?php echo date_sortie(date('d F Y', strtotime($detail['EntreeDate']))); ?><br /><?php } ?>
				<?php if (!empty($detail['NombreSupport'])) { ?><i class="fa fa-dot-circle-o"></i> <strong>Nbre support(s) :</strong> <?php echo $detail['NombreSupport']; ?><br /><?php } ?>
				<?php if (!empty($detail['Zone']) && $menu['type'] == 'video') { ?><i class="fa fa-flag"></i> <strong>Zone :</strong> <?php if (file_exists('./img/zones/'.$detail['Zone'].'.png')) echo '<img src="./img/zones/'.$detail['Zone'].'.png" style="max-width:82px;max-height:25px;" />'; else echo $detail['Zone']; ?><br /><?php } ?>
			</div>
		</div>
		<!-- AUDIO -->
		<?php if (!empty($detail['Audio'])) { ?>
			<div class="panel panel-default">
				<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-comments"></i> Audio</h3></div>
				<div class="panel-body">
					<table width="100%">
						<?php
							$liste_audio = explode(', ', $detail['Audio']);
							for ($i=0;$i<count($liste_audio);$i++)
							{
								preg_match('/^([a-zA-Z-]+)(\d.+) ([a-zA-ZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ]+)/i', $liste_audio[$i], $audio);
								echo '<tr height="30px">';
								// Flags
								if (file_exists('./img/flags/'.clean_img($audio[3]).'.png'))
								{
									echo '<td><img src="./img/flags/'.clean_img($audio[3]).'.png" style="max-width:20px;" title="'.$audio[3].'" /> '.$audio[3].'</td>';
								} else {
									echo '<td>'.$audio[3].'</td>';
								}
								// Audio Codec
								if (file_exists('./img/audiocodec/'.$audio[1].'.png'))
								{
									echo '<td style="width:30%" class="text-center"><img src="./img/audiocodec/'.$audio[1].'.png" style="max-width:82px;max-height:25px;" title="'.$audio[1].'" /></td>';
								} else {
									echo '<td style="width:30%" class="text-center">'.$audio[1].'</td>';
								}
								// Audio Channel
								if (file_exists('./img/audiochannel/'.$audio[2].'.png'))
								{
									echo '<td style="width:30%" class="text-right"><img src="./img/audiochannel/'.$audio[2].'.png" style="max-width:82px;max-height:25px;" title="'.$audio[2].'" /></td>';
								} else {
									echo '<td style="width:30%" class="text-right">'.$audio[2].'</td>';
								}
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
				<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-cc"></i> Sous-titres</h3></div>
				<div class="panel-body">
					<?php
						$soustitres = explode(', ', $detail['SousTitres']);
						for ($i=0;$i<count($soustitres);$i++)
						{
							if (file_exists('./img/flags/'.clean_img($soustitres[$i]).'.png'))
							{
								echo '<img src="./img/flags/'.clean_img($soustitres[$i]).'.png" style="width:20px" title="'.$soustitres[$i].'" /> '.$soustitres[$i].'<br />';
							} else {
								echo $soustitres[$i].'<br />';
							}
						}
					?>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
