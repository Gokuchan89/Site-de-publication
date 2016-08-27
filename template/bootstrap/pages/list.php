<?php
	/*
		=================================
		REGLAGE
		=================================
	*/
	$dp_type_array = array('liste', 'galerie', 'table');
	if (!isset($_SESSION['option_dp_type'])) $_SESSION['option_dp_type'] = 'galerie';
	if (isset($_['option_dp_type']) && in_array($_['option_dp_type'], $dp_type_array)) $_SESSION['option_dp_type'] = $_['option_dp_type'];
	$option_dp_type = $_SESSION['option_dp_type'];

	$nb_elements_array = array('6', '12', '18', '24', '30', '36');
	if (!isset($_SESSION['option_nb_elements'])) $_SESSION['option_nb_elements'] = '24';
	if (isset($_['option_nb_elements']) && in_array($_['option_nb_elements'], $nb_elements_array)) $_SESSION['option_nb_elements'] = $_['option_nb_elements'];
	$option_nb_elements = $_SESSION['option_nb_elements'];
	
	$order_array = array('`TitreVF`', '`TitreVF` DESC', '`Annee`, `ID`', '`Annee` DESC, `ID` DESC', '`ID`', '`ID` DESC');
	if (!isset($_SESSION['option_order'])) $_SESSION['option_order'] = '`TitreVF`';
	if (isset($_['option_order']) && in_array($_['option_order'], $order_array)) $_SESSION['option_order'] = $_['option_order'];
	$option_order = $_SESSION['option_order'];

	/*
		=================================
		MENU
		=================================
	*/
	$query = $db->prepare('SELECT `id`, `name`, `table`, `type` FROM `site_menu` WHERE `id` = :id');
	$query->bindValue(':id', $table, PDO::PARAM_INT);
	$query->execute();
	$menu = $query->fetch();
	$query->closeCursor();

	/*
		=================================
		LISTE -> FILTRES
		=================================
	*/
	$list_filter_query = $db->prepare('SELECT `id`, `name`, `type`, `sort`, `menu`, `position` FROM `site_menu_filter` WHERE `menu` = :menu ORDER BY `position`');
	$list_filter_query->bindValue(':menu', $table, PDO::PARAM_INT);
	$list_filter_query->execute();

	/*
		=================================
		LISTE
		=================================
	*/
	// Total
	$query = $db->prepare('SELECT COUNT(`ID`) FROM `'.$menu['table'].'`');
	$query->execute();
	$list_total = $query->fetchColumn();
	$query->closeCursor();

	// Page
	if (isset($_['page']) && is_numeric($_['page']))
	{
		if ($_['page'] >= 1 && $_['page'] <= ceil($list_total/$option_nb_elements)) $page = intval($_['page']); else $page = 1;
	} else {
		$page = 1;
	}
	$offset_list = ($page-1) * $option_nb_elements;

	$list_search = '';

	// Recherche + Filtres
	if (!isset($_SESSION[$menu['table'].'_search_value'])) $_SESSION[$menu['table'].'_search_value'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_genre'])) $_SESSION[$menu['table'].'_search_value_genre'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_pays'])) $_SESSION[$menu['table'].'_search_value_pays'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_annee'])) $_SESSION[$menu['table'].'_search_value_annee'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_duree'])) $_SESSION[$menu['table'].'_search_value_duree'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_note'])) $_SESSION[$menu['table'].'_search_value_note'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_filmvu'])) $_SESSION[$menu['table'].'_search_value_filmvu'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_commentaires'])) $_SESSION[$menu['table'].'_search_value_commentaires'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_reference'])) $_SESSION[$menu['table'].'_search_value_reference'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_support'])) $_SESSION[$menu['table'].'_search_value_support'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_edition'])) $_SESSION[$menu['table'].'_search_value_edition'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_zone'])) $_SESSION[$menu['table'].'_search_value_zone'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_soustitres'])) $_SESSION[$menu['table'].'_search_value_soustitres'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_audio'])) $_SESSION[$menu['table'].'_search_value_audio'] = '';
	
	if (isset($_[$menu['table'].'_search_value'])) $_SESSION[$menu['table'].'_search_value'] = $_[$menu['table'].'_search_value'];
	if (isset($_[$menu['table'].'_search_value_genre'])) $_SESSION[$menu['table'].'_search_value_genre'] = $_[$menu['table'].'_search_value_genre'];
	if (isset($_[$menu['table'].'_search_value_pays'])) $_SESSION[$menu['table'].'_search_value_pays'] = $_[$menu['table'].'_search_value_pays'];
	if (isset($_[$menu['table'].'_search_value_annee'])) $_SESSION[$menu['table'].'_search_value_annee'] = $_[$menu['table'].'_search_value_annee'];
	if (isset($_[$menu['table'].'_search_value_duree'])) $_SESSION[$menu['table'].'_search_value_duree'] = $_[$menu['table'].'_search_value_duree'];
	if (isset($_[$menu['table'].'_search_value_note'])) $_SESSION[$menu['table'].'_search_value_note'] = $_[$menu['table'].'_search_value_note'];
	if (isset($_[$menu['table'].'_search_value_filmvu'])) $_SESSION[$menu['table'].'_search_value_filmvu'] = $_[$menu['table'].'_search_value_filmvu'];
	if (isset($_[$menu['table'].'_search_value_commentaires'])) $_SESSION[$menu['table'].'_search_value_commentaires'] = $_[$menu['table'].'_search_value_commentaires'];
	if (isset($_[$menu['table'].'_search_value_reference'])) $_SESSION[$menu['table'].'_search_value_reference'] = $_[$menu['table'].'_search_value_reference'];
	if (isset($_[$menu['table'].'_search_value_support'])) $_SESSION[$menu['table'].'_search_value_support'] = $_[$menu['table'].'_search_value_support'];
	if (isset($_[$menu['table'].'_search_value_edition'])) $_SESSION[$menu['table'].'_search_value_edition'] = $_[$menu['table'].'_search_value_edition'];
	if (isset($_[$menu['table'].'_search_value_zone'])) $_SESSION[$menu['table'].'_search_value_zone'] = $_[$menu['table'].'_search_value_zone'];
	if (isset($_[$menu['table'].'_search_value_soustitres'])) $_SESSION[$menu['table'].'_search_value_soustitres'] = $_[$menu['table'].'_search_value_soustitres'];
	if (isset($_[$menu['table'].'_search_value_audio'])) $_SESSION[$menu['table'].'_search_value_audio'] = $_[$menu['table'].'_search_value_audio'];
	
	if (!empty($_SESSION[$menu['table'].'_search_value']) || !empty($_SESSION[$menu['table'].'_search_value_genre']) || !empty($_SESSION[$menu['table'].'_search_value_pays']) || !empty($_SESSION[$menu['table'].'_search_value_annee']) || !empty($_SESSION[$menu['table'].'_search_value_duree']) || !empty($_SESSION[$menu['table'].'_search_value_note']) || !empty($_SESSION[$menu['table'].'_search_value_filmvu']) || !empty($_SESSION[$menu['table'].'_search_value_commentaires']) || !empty($_SESSION[$menu['table'].'_search_value_reference']) || !empty($_SESSION[$menu['table'].'_search_value_support']) || !empty($_SESSION[$menu['table'].'_search_value_edition']) || !empty($_SESSION[$menu['table'].'_search_value_zone']) || !empty($_SESSION[$menu['table'].'_search_value_soustitres']) || !empty($_SESSION[$menu['table'].'_search_value_audio']))
	{
		if (!empty($_SESSION[$menu['table'].'_search_value'])) $list_search .= ' AND (`TitreVF` LIKE "%'.$_SESSION[$menu['table'].'_search_value'].'%" OR `TitreVO` LIKE "%'.$_SESSION[$menu['table'].'_search_value'].'%" OR `Acteurs` LIKE "%'.$_SESSION[$menu['table'].'_search_value'].'%" OR `Realisateurs` LIKE "%'.$_SESSION[$menu['table'].'_search_value'].'%")';
		if (!empty($_SESSION[$menu['table'].'_search_value_genre'])) $list_search .= ' AND `Genre` LIKE "%'.$_SESSION[$menu['table'].'_search_value_genre'].'%"';
		if (!empty($_SESSION[$menu['table'].'_search_value_pays'])) $list_search .= ' AND `Pays` LIKE "%'.$_SESSION[$menu['table'].'_search_value_pays'].'%"';
		if (!empty($_SESSION[$menu['table'].'_search_value_annee'])) $list_search .= ' AND `Annee` = "'.$_SESSION[$menu['table'].'_search_value_annee'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_duree'])) $list_search .= ' AND `Duree` = "'.$_SESSION[$menu['table'].'_search_value_duree'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_note'])) $list_search .= ' AND `Note` = "'.$_SESSION[$menu['table'].'_search_value_note'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_filmvu'])) $list_search .= ' AND `FilmVu` = "'.$_SESSION[$menu['table'].'_search_value_filmvu'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_commentaires'])) $list_search .= ' AND `Commentaires` = "'.$_SESSION[$menu['table'].'_search_value_commentaires'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_reference'])) $list_search .= ' AND `Reference` = "'.$_SESSION[$menu['table'].'_search_value_reference'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_support'])) $list_search .= ' AND `Support` = "'.$_SESSION[$menu['table'].'_search_value_support'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_edition'])) $list_search .= ' AND `Edition` = "'.$_SESSION[$menu['table'].'_search_value_edition'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_zone'])) $list_search .= ' AND `Zone` = "'.$_SESSION[$menu['table'].'_search_value_zone'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_soustitres'])) $list_search .= ' AND `SousTitres` LIKE "%'.$_SESSION[$menu['table'].'_search_value_soustitres'].'%"';
		if (!empty($_SESSION[$menu['table'].'_search_value_audio'])) $list_search .= ' AND `Audio` LIKE "%'.$_SESSION[$menu['table'].'_search_value_audio'].'%"';
	}

	$query = $db->prepare('SELECT COUNT(`ID`) FROM `'.$menu['table'].'` WHERE `Sortie` = "NON" '.$list_search);
	$query->execute();
	$list_search_total = $query->fetchColumn();
	$query->closeCursor();

	// Liste
	$listing_query = $db->prepare('SELECT `ID`, `TitreVF`, `Genre`, `Annee`, `Duree`, `Note`, `Realisateurs`, `Support` FROM `'.$menu['table'].'` WHERE `Note` >= "0" '.$list_search.' ORDER BY '.$option_order.' LIMIT '.$option_nb_elements.' OFFSET '.$offset_list);
	$listing_query->execute();
?>
<script>document.title += " / <?php echo $menu['name']; ?>"</script>


<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
	<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="heading">
			<h4 class="panel-title">
				Recherche + Filtres
				<div class="pull-right"><a href="#collapse" data-toggle="collapse" class="a-box-tool"><i class="fa fa-<?php if ($_SESSION[$menu['table'].'_search_value'] != '' || $_SESSION[$menu['table'].'_search_value_genre'] != '' || $_SESSION[$menu['table'].'_search_value_pays'] != '' || $_SESSION[$menu['table'].'_search_value_annee'] != '' || $_SESSION[$menu['table'].'_search_value_duree'] != '' || $_SESSION[$menu['table'].'_search_value_note'] != '' || $_SESSION[$menu['table'].'_search_value_filmvu'] != '' || $_SESSION[$menu['table'].'_search_value_commentaires'] != '' || $_SESSION[$menu['table'].'_search_value_reference'] != '' || $_SESSION[$menu['table'].'_search_value_support'] != '' || $_SESSION[$menu['table'].'_search_value_edition'] != '' || $_SESSION[$menu['table'].'_search_value_zone'] != '' || $_SESSION[$menu['table'].'_search_value_soustitres'] != '' || $_SESSION[$menu['table'].'_search_value_audio'] != '') echo 'minus'; else echo 'plus'; ?>"></i></a></div>
			</h4>
		</div>
		<div class="panel-collapse collapse <?php if ($_SESSION[$menu['table'].'_search_value'] != '' || $_SESSION[$menu['table'].'_search_value_genre'] != '' || $_SESSION[$menu['table'].'_search_value_pays'] != '' || $_SESSION[$menu['table'].'_search_value_annee'] != '' || $_SESSION[$menu['table'].'_search_value_duree'] != '' || $_SESSION[$menu['table'].'_search_value_note'] != '' || $_SESSION[$menu['table'].'_search_value_filmvu'] != '' || $_SESSION[$menu['table'].'_search_value_commentaires'] != '' || $_SESSION[$menu['table'].'_search_value_reference'] != '' || $_SESSION[$menu['table'].'_search_value_support'] != '' || $_SESSION[$menu['table'].'_search_value_edition'] != '' || $_SESSION[$menu['table'].'_search_value_zone'] != '' || $_SESSION[$menu['table'].'_search_value_soustitres'] != '' || $_SESSION[$menu['table'].'_search_value_audio'] != '') echo 'in'; ?>" id="collapse">
			<div class="panel-body">
				<div class="form-group">
					<label>Recherche par</label>
					<form method="POST" action="?op=list&table=<?php echo $table; ?>" id="searchForm">
						<div class="input-group">
							<input type="text" class="form-control" name="<?php echo $menu['table']; ?>_search_value" value="<?php echo $_SESSION[$menu['table'].'_search_value']; ?>" id="searchField" placeholder="Titre, acteurs, réalisateurs" />
							<div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></div>
							<?php if (!empty($_SESSION[$menu['table'].'_search_value'])) { ?>
								<div class="input-group-btn"><button type="submit" class="btn btn-primary" name="<?php echo $menu['table']; ?>_search_value"><i class="fa fa-close"></i></button></div>
							<?php } ?>
						</div>
					</form>
				</div>
				<div class="row">
					<?php while ($list_filter = $list_filter_query->fetch()) { ?>
						<div class="col-xs-12 col-sm-12 col-md-2">
							<div class="form-group">
								<label>Filtrer par <?php echo $list_filter['name']; ?></label>
								<form method="POST" action="?op=list&table=<?php echo $table; ?>">
									<div class="input-group">
										<select class="form-control select2-list-<?php echo $list_filter['type']; ?>" name="<?php echo $menu['table']; ?>_search_value_<?php echo $list_filter['type']; ?>" onchange="this.form.submit()" style="width:100%;">
											<option></option>
											<?php
												$query = $db->prepare('SELECT DISTINCT `'.$list_filter['type'].'` FROM `'.$menu['table'].'` WHERE `Note` >= "0" '.$list_search);
												$query->execute();
												$i = 0;
												$tempo_list = array();
												while ($nf_list = $query->fetch())
												{
													$unique_list = explode(' / ', $nf_list[$list_filter['type']]);
													foreach ($unique_list as $key => $value)
													{
														$unique_list2 = explode(' - ', $value);
														foreach ($unique_list2 as $key => $value)
														{
															$unique_list2 = explode(', ', $value);
															foreach ($unique_list2 as $key => $value)
															{
																$tempo_list[$i] = $value;
																$i++;
															}
														}
													}
												}
												$query->closeCursor();
												$list = array_unique($tempo_list);
												$list_filter['sort']($list);
												foreach ($list as $key => $value)
												{
													if ($_SESSION[$menu['table'].'_search_value_'.$list_filter['type']] == $value) $nfselect = 'selected'; else $nfselect = '';
													echo '<option value="'.$value.'" '.$nfselect.'>'.$value.'</option>';
												}
											?>
										</select>
										<?php if (!empty($_SESSION[$menu['table'].'_search_value_'.$list_filter['type']])) { ?>
											<div class="input-group-btn"><button type="submit" class="btn btn-primary" name="<?php echo $menu['table']; ?>_search_value_<?php echo $list_filter['type']; ?>"><i class="fa fa-close"></i></button></div>
										<?php } ?>
									</div>
								</form>
							</div>
						</div>
					<?php } $list_filter_query->closeCursor(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if ($list_search_total != 0) { ?>
	<div class="panel-group">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-4">
						<div class="form-group">
							<label>Afficher en</label>
							<form method="POST">
								<select class="form-control select2" name="option_dp_type" onchange="this.form.submit()" style="width:100%;">
									<option value="liste" <?php if ($option_dp_type == 'liste') echo 'selected'; ?>>Liste</option>
									<option value="galerie" <?php if ($option_dp_type == 'galerie') echo 'selected'; ?>>Galerie</option>
									<option value="table" <?php if ($option_dp_type == 'table') echo 'selected'; ?>>Table</option>
								</select>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Afficher x éléments</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select class="form-control select2" name="option_nb_elements" onchange="this.form.submit()" style="width:100%;">
									<option value="6" <?php if ($option_nb_elements == '6') echo 'selected'; ?>>6</option>
									<option value="12" <?php if ($option_nb_elements == '12') echo 'selected'; ?>>12</option>
									<option value="18" <?php if ($option_nb_elements == '18') echo 'selected'; ?>>18</option>
									<option value="24" <?php if ($option_nb_elements == '24') echo 'selected'; ?>>24</option>
									<option value="30" <?php if ($option_nb_elements == '30') echo 'selected'; ?>>30</option>
									<option value="36" <?php if ($option_nb_elements == '36') echo 'selected'; ?>>36</option>
								</select>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Trier par</label>
							<form method="POST">
								<select class="form-control select2" name="option_order" onchange="this.form.submit()" style="width:100%;">
									<option value="`TitreVF`" <?php if ($option_order == '`TitreVF`') echo 'selected'; ?>>Titre</option>
									<option value="`TitreVF` DESC" <?php if ($option_order == '`TitreVF` DESC') echo 'selected'; ?>>Titre (desc)</option>
									<option value="`Annee`, `ID`" <?php if ($option_order == '`Annee`, `ID`') echo 'selected'; ?>>Année</option>
									<option value="`Annee` DESC, `ID` DESC" <?php if ($option_order == '`Annee` DESC, `ID` DESC') echo 'selected'; ?>>Année (desc)</option>
									<option value="`ID`" <?php if ($option_order == '`ID`') echo 'selected'; ?>>Date d'ajout</option>
									<option value="`ID` DESC" <?php if ($option_order == '`ID` DESC') echo 'selected'; ?>>Date d'ajout (desc)</option>
								</select>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
<?php
	if ($_SESSION[$menu['table'].'_search_value'] != '' || $_SESSION[$menu['table'].'_search_value_genre'] != '' || $_SESSION[$menu['table'].'_search_value_pays'] != '' || $_SESSION[$menu['table'].'_search_value_annee'] != '' || $_SESSION[$menu['table'].'_search_value_duree'] != '' || $_SESSION[$menu['table'].'_search_value_note'] != '' || $_SESSION[$menu['table'].'_search_value_filmvu'] != '' || $_SESSION[$menu['table'].'_search_value_commentaires'] != '' || $_SESSION[$menu['table'].'_search_value_reference'] != '' || $_SESSION[$menu['table'].'_search_value_support'] != '' || $_SESSION[$menu['table'].'_search_value_edition'] != '' || $_SESSION[$menu['table'].'_search_value_zone'] != '' || $_SESSION[$menu['table'].'_search_value_soustitres'] != '' || $_SESSION[$menu['table'].'_search_value_audio'] != '')
	{
		if ($list_search_total == 0)
		{
			echo '<div class="alert alert-danger"><strong>0</strong> résultat</div>';
		} elseif ($list_search_total == 1) {
			echo '<div class="alert alert-success"><strong>'.$list_search_total.'</strong> résultat</div>';
		} else {
			echo '<div class="alert alert-success"><strong>'.$list_search_total.'</strong> résultats</div>';
		}
	}
?>
<?php
	$total = $list_search_total;			// nombre d'entrées dans la table
	$epp = $option_nb_elements; 			// nombre d'entrées à afficher par page
	$nbPages = ceil($total/$epp); 			// calcul du nombre de pages $nbPages (on arrondit à l'entier supérieur avec la fonction ceil())
	// Récupération du numéro de la page courante depuis l'URL avec la méthode GET
	// S'il s'agit d'un nombre on traite, sinon on garde la valeur par défaut : 1
	$current = 1;
	if (isset($_GET['page']) && is_numeric($_GET['page']))
	{
		$page = intval($_GET['page']);
		if ($page >= 1 && $page <= $nbPages) $current = $page;
		elseif ($page < 1) $current = 1;
		else $current = 1;
	}
	if ($nbPages > 1) echo '<nav class="text-center"><ul class="pagination">'.paginate('?op=list&table='.$table.'', '&page=', $nbPages, $current).'</ul></nav>';
?>
<?php if ($option_dp_type == 'liste') { ?>
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th style="width:20%">Image</th>
				<th>Details</th>
			</tr>
		</thead>
		<tbody>
			<?php while ($listing = $listing_query->fetch()) { ?>
				<tr style="height:200px">
					<td class="text-center">
						<?php $filename = sprintf('./profils/'.$menu['table'].'/affiches/Filmotech_%05d.jpg', $listing['ID']); ?>
						<?php if (file_exists($filename)) echo '<div class="list"><img data-original="'.$filename.'" class="list-img lazy" alt="affiche" /></div>'; else echo '<div class="list"><img data-src="holder.js/100px165?text=aucune \n image" alt="affiche" /></div>'; ?>
					</td>
					<td>
						<div class="row"><div class="col-xs-12 col-sm-12 col-md-12"><a href="./?op=detail&table=<?php echo $menu['id']; ?>&id=<?php echo $listing['ID']; ?>"><?php echo $listing['TitreVF']; ?></a></div></div>
						<?php if (!empty($listing['Realisateurs'])) { ?>
							<div class="row">
								<div class="col-xs-4 col-sm-4 col-md-3"><strong><?php if ($menu['type'] == 'jeuxvideo') echo 'Editeur(s) / Développeur(s)'; if ($menu['type'] == 'livre') echo 'Auteur(s)'; if ($menu['type'] == 'musique') echo 'Artiste(s) / Groupe'; if ($menu['type'] == 'video') echo 'Réalisateur(s)'; ?></strong></div>
								<div class="col-xs-8 col-sm-8 col-md-9"><span class="text-danger"><?php echo str_replace("\r", ' / ', $listing['Realisateurs']); ?></span></div>
							</div>
						<?php } ?>
						<?php if ($menu['type'] == 'jeuxvideo' || $menu['type'] == 'musique' || $menu['type'] == 'video') { ?>
							<div class="row">
								<div class="col-xs-4 col-sm-4 col-md-3"><strong>Support</strong></div>
								<div class="col-xs-8 col-sm-8 col-md-9"><span class="text-danger"><?php echo $listing['Support']; ?></span></div>
							</div>
						<?php } ?>
						<div class="row">
							<div class="col-xs-4 col-sm-4 col-md-3"><strong>Genre</strong></div>
							<div class="col-xs-8 col-sm-8 col-md-9"><span class="text-danger"><?php echo str_replace(' - ', ' / ', $listing['Genre']); ?></span></div>
						</div>
						<div class="row">
							<div class="col-xs-4 col-sm-4 col-md-3"><strong>Année</strong></div>
							<div class="col-xs-8 col-sm-8 col-md-9"><span class="text-danger"><?php echo $listing['Annee']; ?></span></div>
						</div>
						<?php
							if ($listing['Duree'] != '0')
							{
								echo '<div class="row">';
								if ($menu['type'] == 'livre')
								{
									echo '<div class="col-xs-4 col-sm-4 col-md-3"><strong>Pages</strong></div>';
									echo '<div class="col-xs-8 col-sm-8 col-md-9"><span class="text-danger">'.$listing['Duree'].'</span></div>';
								}
								if ($menu['type'] == 'musique' || $menu['type'] == 'video')
								{
									if ($listing['Duree'] < '60')
									{
										$duree = ($listing['Duree']%60).'min';
									}
									elseif ($listing['Duree'] > '60')
									{
										$duree = floor($listing['Duree']/60).'h '.($listing['Duree']%60).'min';
									}
									echo '<div class="col-xs-4 col-sm-4 col-md-3"><strong>Durée</strong></div>';
									echo '<div class="col-xs-8 col-sm-8 col-md-9"><span class="text-danger">'.$duree.'</span></div>';
								}
								echo '</div>';
							}
						?>
						<?php if ($menu['type'] == 'video') { ?>
							<div class="row">
								<div class="col-xs-4 col-sm-4 col-md-3"><strong>Note</strong></div>
								<div class="col-xs-8 col-sm-8 col-md-9"><span class="text-danger"><?php if (file_exists('./img/stars/'.$listing['Note'].'.png')) echo '<img src="img/stars/'.$listing['Note'].'.png" />'; else echo $listing['Note']; ?></span></div>
							</div>
						<?php } ?>
					</td>
				</tr>
			<?php } $listing_query->closeCursor(); ?>
		</tbody>
	</table>
<?php } ?>
<?php if ($option_dp_type == 'galerie') { ?>
	<div class="row text-center">
		<?php while ($listing = $listing_query->fetch()) { ?>
			<div class="col-xs-6 col-sm-4 col-md-2">
				<a href="./?op=detail&table=<?php echo $menu['id']; ?>&id=<?php echo $listing['ID']; ?>">
					<div class="thumbnail">
						<?php $filename = sprintf('./profils/'.$menu['table'].'/affiches/Filmotech_%05d.jpg', $listing['ID']); ?>
						<?php if (file_exists($filename)) echo '<div class="list"><img data-original="'.$filename.'" class="list-img lazy" alt="affiche" /></div>'; else echo '<div class="list"><img data-src="holder.js/100px165?text=aucune \n image" alt="affiche" /></div>'; ?>
						<div class="list-year text-danger"><?php echo $listing['Annee']; ?></div>
						<div class="list-title text-info"><?php echo $listing['TitreVF']; ?></div>
					</div>
				</a>
			</div>
		<?php } $listing_query->closeCursor(); ?>
	</div>
<?php } ?>
<?php if ($option_dp_type == 'table') { ?>
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Titre</th>
				<?php if ($menu['type'] == 'jeuxvideo' || $menu['type'] == 'musique' || $menu['type'] == 'video') echo '<th style="width:9%">Support</th>'; ?>
				<th style="width:15%">Genre</th>
				<th style="width:9%">Année</th>
				<?php if ($menu['type'] == 'livre') echo '<th style="width:9%">Pages</th>'; if ($menu['type'] == 'musique' || $menu['type'] == 'video') echo '<th style="width:9%">Durée</th>'; ?>
			</tr>
		</thead>
		<tbody>
			<?php while ($listing = $listing_query->fetch()) { ?>
				<tr>
					<td><a href="./?op=detail&table=<?php echo $menu['id']; ?>&id=<?php echo $listing['ID']; ?>"><?php echo $listing['TitreVF']; ?></a></td>
					<?php if ($menu['type'] == 'jeuxvideo' || $menu['type'] == 'musique' || $menu['type'] == 'video') echo '<td>'.$listing['Support'].'</td>'; ?>
					<td><?php echo $listing['Genre']; ?></td>
					<td><?php echo $listing['Annee']; ?></td>
					<?php
						if ($menu['type'] == 'livre')
						{
							if ($listing['Duree'] != '0')
							{
								echo '<td>'.$listing['Duree'].'</td>';
							} else {
								echo '<td></td>';
							}
						}
						if ($menu['type'] == 'musique' || $menu['type'] == 'video')
						{
							if ($listing['Duree'] != '0' && $listing['Duree'] < '60')
							{
								echo '<td>'.($listing['Duree']%60).'min</td>';;
							}
							elseif ($listing['Duree'] != '0' && $listing['Duree'] > '60')
							{
								echo '<td>'.floor($listing['Duree']/60).'h '.($listing['Duree']%60).'min</td>';
							} else {
								echo '<td></td>';
							}
						}
					?>
				</tr>
			<?php } $listing_query->closeCursor(); ?>
		</tbody>
	</table>
<?php } ?>
<?php
	$total = $list_search_total;			// nombre d'entrées dans la table
	$epp = $option_nb_elements; 			// nombre d'entrées à afficher par page
	$nbPages = ceil($total/$epp); 			// calcul du nombre de pages $nbPages (on arrondit à l'entier supérieur avec la fonction ceil())
	// Récupération du numéro de la page courante depuis l'URL avec la méthode GET
	// S'il s'agit d'un nombre on traite, sinon on garde la valeur par défaut : 1
	$current = 1;
	if (isset($_GET['page']) && is_numeric($_GET['page']))
	{
		$page = intval($_GET['page']);
		if ($page >= 1 && $page <= $nbPages) $current = $page;
		elseif ($page < 1) $current = 1;
		else $current = 1;
	}
	if ($nbPages > 1) echo '<nav class="text-center"><ul class="pagination">'.paginate('?op=list&table='.$table.'', '&page=', $nbPages, $current).'</ul></nav>';
?>
