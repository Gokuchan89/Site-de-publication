<?php
	/*
		=================================
		REGLAGE
		=================================
	*/
	$order_array = array('TitreVF', 'TitreVF DESC', 'Annee', 'Annee DESC', 'EntreeDate', 'EntreeDate DESC');
	if (!isset($_SESSION['option_order'])) $_SESSION['option_order'] = 'TitreVF';
	if (isset($_['option_order']) && in_array($_['option_order'], $order_array)) $_SESSION['option_order'] = $_['option_order'];
	$option_order = $_SESSION['option_order'];

	$nb_elements_array = array('6', '12', '18', '24', '30', '36');
	if (!isset($_SESSION['option_nb_elements'])) $_SESSION['option_nb_elements'] = '24';
	if (isset($_['option_nb_elements']) && in_array($_['option_nb_elements'], $nb_elements_array)) $_SESSION['option_nb_elements'] = $_['option_nb_elements'];
	$option_nb_elements = $_SESSION['option_nb_elements'];

	$dp_type_array = array('liste', 'galerie', 'table');
	if (!isset($_SESSION['option_dp_type'])) $_SESSION['option_dp_type'] = 'galerie';
	if (isset($_['option_dp_type']) && in_array($_['option_dp_type'], $dp_type_array)) $_SESSION['option_dp_type'] = $_['option_dp_type'];
	$option_dp_type = $_SESSION['option_dp_type'];

	$menu_query = $db->prepare('SELECT `id`, `name`, `table`, `type` FROM `site_menu` WHERE `id` = :id');
	$menu_query->bindValue(':id', $table, PDO::PARAM_INT);
	$menu_query->execute();
	$menu = $menu_query->fetch();
	$menu_query->closeCursor();

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
	if (!isset($_SESSION[$menu['table'].'_search_value_support'])) $_SESSION[$menu['table'].'_search_value_support'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_filmvu'])) $_SESSION[$menu['table'].'_search_value_filmvu'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_genre'])) $_SESSION[$menu['table'].'_search_value_genre'] = '';
	if (!isset($_SESSION[$menu['table'].'_search_value_annee'])) $_SESSION[$menu['table'].'_search_value_annee'] = '';

	if (isset($_[$menu['table'].'_search_value'])) $_SESSION[$menu['table'].'_search_value'] = $_[$menu['table'].'_search_value'];
	if (isset($_[$menu['table'].'_search_value_support'])) $_SESSION[$menu['table'].'_search_value_support'] = $_[$menu['table'].'_search_value_support'];
	if (isset($_[$menu['table'].'_search_value_filmvu'])) $_SESSION[$menu['table'].'_search_value_filmvu'] = $_[$menu['table'].'_search_value_filmvu'];
	if (isset($_[$menu['table'].'_search_value_genre'])) $_SESSION[$menu['table'].'_search_value_genre'] = $_[$menu['table'].'_search_value_genre'];
	if (isset($_[$menu['table'].'_search_value_annee'])) $_SESSION[$menu['table'].'_search_value_annee'] = $_[$menu['table'].'_search_value_annee'];

	if (!empty($_SESSION[$menu['table'].'_search_value']) || !empty($_SESSION[$menu['table'].'_search_value_support']) || !empty($_SESSION[$menu['table'].'_search_value_filmvu']) || !empty($_SESSION[$menu['table'].'_search_value_genre']) || !empty($_SESSION[$menu['table'].'_search_value_annee']))
	{
		if (!empty($_SESSION[$menu['table'].'_search_value'])) $list_search .= ' AND (`TitreVF` LIKE "%'.$_SESSION[$menu['table'].'_search_value'].'%" OR `TitreVO` LIKE "%'.$_SESSION[$menu['table'].'_search_value'].'%" OR `Acteurs` LIKE "%'.$_SESSION[$menu['table'].'_search_value'].'%" OR `Realisateurs` LIKE "%'.$_SESSION[$menu['table'].'_search_value'].'%")';
		if (!empty($_SESSION[$menu['table'].'_search_value_support'])) $list_search .= ' AND `Support` = "'.$_SESSION[$menu['table'].'_search_value_support'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_filmvu'])) $list_search .= ' AND `FilmVu` = "'.$_SESSION[$menu['table'].'_search_value_filmvu'].'"';
		if (!empty($_SESSION[$menu['table'].'_search_value_genre'])) $list_search .= ' AND `Genre` LIKE "%'.$_SESSION[$menu['table'].'_search_value_genre'].'%"';
		if (!empty($_SESSION[$menu['table'].'_search_value_annee'])) $list_search .= ' AND `Annee` = "'.$_SESSION[$menu['table'].'_search_value_annee'].'"';
	}

	$query = $db->prepare('SELECT COUNT(`ID`) FROM `'.$menu['table'].'` WHERE `Sortie` = "NON" '.$list_search);
	$query->execute();
	$list_search_total = $query->fetchColumn();
	$query->closeCursor();

	// Liste
	$listing_query = $db->prepare('SELECT `ID`, `TitreVF`, `Genre`, `Annee`, `Duree`, `Note`, `Realisateurs`, `Support` FROM `'.$menu['table'].'` WHERE `Note` >= "0" '.$list_search.' ORDER BY '.$option_order.' LIMIT '.$option_nb_elements.' OFFSET '.$offset_list);
	$listing_query->execute();

	// Liste par support
	$query = $db->prepare('SELECT DISTINCT `Support` FROM `'.$menu['table'].'` WHERE `Note` >= "0" '.$list_search);
	$query->execute();
	$i = 0;
	$tempo_list = array();
	while ($nf_list = $query->fetch())
	{
		$unique_list = array($nf_list['Support']);
		foreach ($unique_list as $key => $value)
		{
			$tempo_list[$i] = $value;
			$i++;
		}
	}
	$query->closeCursor();
	$list_support = array_unique($tempo_list);
	sort($list_support);

	// Liste par film vu
	$query = $db->prepare('SELECT DISTINCT `FilmVu` FROM `'.$menu['table'].'` WHERE `Note` >= "0" '.$list_search);
	$query->execute();
	$i = 0;
	$tempo_list = array();
	while ($nf_list = $query->fetch())
	{
		$unique_list = array($nf_list['FilmVu']);
		foreach ($unique_list as $key => $value)
		{
			$tempo_list[$i] = $value;
			$i++;
		}
	}
	$query->closeCursor();
	$list_filmvu = array_unique($tempo_list);
	sort($list_filmvu);

	// Liste par genre
	$query = $db->prepare('SELECT DISTINCT `Genre` FROM `'.$menu['table'].'` WHERE `Note` >= "0" '.$list_search);
	$query->execute();
	$i = 0;
	$tempo_list = array();
	while ($nf_list = $query->fetch())
	{
		$unique_list = explode(' / ', $nf_list['Genre']);
		foreach ($unique_list as $key => $value)
		{
			$unique_list2 = explode(' - ', $value);
			foreach ($unique_list2 as $key => $value)
			{
				$tempo_list[$i] = $value;
				$i++;
			}
		}
	}
	$query->closeCursor();
	$list_genre = array_unique($tempo_list);
	sort($list_genre);

	// Liste par annee
	$query = $db->prepare('SELECT DISTINCT `Annee` FROM `'.$menu['table'].'` WHERE `Note` >= "0" '.$list_search);
	$query->execute();
	$i = 0;
	$tempo_list = array();
	while ($nf_list = $query->fetch())
	{
		$unique_list = array($nf_list['Annee']);
		foreach ($unique_list as $key => $value)
		{
			$tempo_list[$i] = $value;
			$i++;
		}
	}
	$query->closeCursor();
	$list_annee = array_unique($tempo_list);
	sort($list_annee);
?>
<script>document.title += " / Liste / <?php echo $menu['name']; ?>"</script>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<div class="row">
				<div class="col-xs-12 col-sm-12 <?php if ($menu['type'] == 'jeuxvideo' || $menu['type'] == 'musique') echo 'col-md-3'; elseif ($menu['type'] == 'video') echo 'col-md-4'; else echo 'col-md-4' ?>">
					<div class="form-group">
						<label>Recherche</label>
						<form method="POST" action="?op=list&table=<?php echo $table; ?>" id="searchForm">
							<div class="input-group">
								<input type="text" class="form-control" name="<?php echo $menu['table']; ?>_search_value" value="<?php echo $_SESSION[$menu['table'].'_search_value']; ?>" id="searchField" />
								<div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></div>
								<?php if (!empty($_SESSION[$menu['table'].'_search_value'])) { ?>
									<div class="input-group-btn"><button type="submit" class="btn btn-primary" name="<?php echo $menu['table']; ?>_search_value"><i class="fa fa-close"></i></button></div>
								<?php } ?>
							</div>
						</form>
					</div>
				</div>
				<?php if ($menu['type'] == 'jeuxvideo' || $menu['type'] == 'musique' || $menu['type'] == 'video') { ?>
					<div class="col-xs-12 <?php if ($menu['type'] == 'video') echo 'col-sm-3 col-md-2'; else echo 'col-sm-4 col-md-3' ?>">
						<div class="form-group">
							<label>Filtrer par support</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<div class="input-group">
									<select class="form-control select2-support" name="<?php echo $menu['table']; ?>_search_value_support" onchange="this.form.submit()" style="width:100%;">
										<option></option>
										<?php
											foreach ($list_support as $key => $value1)
											{
												if ($_SESSION[$menu['table'].'_search_value_support'] == $value1) $nfselect = 'selected'; else $nfselect = '';
												echo '<option value="'.$value1.'" '.$nfselect.'>'.$value1.'</option>';
											}
										?>
									</select>
									<?php if (!empty($_SESSION[$menu['table'].'_search_value_support'])) { ?>
										<div class="input-group-btn"><button type="submit" class="btn btn-primary" name="<?php echo $menu['table']; ?>_search_value_support"><i class="fa fa-close"></i></button></div>
									<?php } ?>
								</div>
							</form>
						</div>
					</div>
				<?php } ?>
				<?php if ($menu['type'] == 'video') { ?>
					<div class="col-xs-12 col-sm-3 col-md-2">
						<div class="form-group">
							<label>Filtrer par vu/non vu</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<div class="input-group">
									<select class="form-control select2-filmvu" name="<?php echo $menu['table']; ?>_search_value_filmvu" onchange="this.form.submit()" style="width:100%;">
										<option></option>
										<?php
											foreach ($list_filmvu as $key => $value2)
											{
												if ($_SESSION[$menu['table'].'_search_value_filmvu'] == $value2) $nfselect = 'selected'; else $nfselect = '';
												echo '<option value="'.$value2.'" '.$nfselect.'>'.$value2.'</option>';
											}
										?>
									</select>
									<?php if (!empty($_SESSION[$menu['table'].'_search_value_filmvu'])) { ?>
										<div class="input-group-btn"><button type="submit" class="btn btn-primary" name="<?php echo $menu['table']; ?>_search_value_filmvu"><i class="fa fa-close"></i></button></div>
									<?php } ?>
								</div>
							</form>
						</div>
					</div>
				<?php } ?>
				<div class="col-xs-12 <?php if ($menu['type'] == 'jeuxvideo' || $menu['type'] == 'musique') echo 'col-sm-4 col-md-3'; elseif ($menu['type'] == 'video') echo 'col-sm-3 col-md-2'; else echo 'col-sm-6 col-md-4' ?>">
					<div class="form-group">
						<label>Filtrer par genre</label>
						<form method="POST" action="?op=list&table=<?php echo $table; ?>">
							<div class="input-group">
								<select class="form-control select2-genre" name="<?php echo $menu['table']; ?>_search_value_genre" onchange="this.form.submit()" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_genre as $key => $value2)
										{
											if ($_SESSION[$menu['table'].'_search_value_genre'] == $value2) $nfselect = 'selected'; else $nfselect = '';
											echo '<option value="'.$value2.'" '.$nfselect.'>'.$value2.'</option>';
										}
									?>
								</select>
								<?php if (!empty($_SESSION[$menu['table'].'_search_value_genre'])) { ?>
									<div class="input-group-btn"><button type="submit" class="btn btn-primary" name="<?php echo $menu['table']; ?>_search_value_genre"><i class="fa fa-close"></i></button></div>
								<?php } ?>
							</div>
						</form>
					</div>
				</div>
				<div class="col-xs-12 <?php if ($menu['type'] == 'jeuxvideo' || $menu['type'] == 'musique') echo 'col-sm-4 col-md-3'; elseif ($menu['type'] == 'video') echo 'col-sm-3 col-md-2'; else echo 'col-sm-6 col-md-4' ?>">
					<div class="form-group">
						<label>Filtrer par année</label>
						<form method="POST" action="?op=list&table=<?php echo $table; ?>">
							<div class="input-group">
								<select class="form-control select2-annee" name="<?php echo $menu['table']; ?>_search_value_annee" onchange="this.form.submit()" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_annee as $key => $value)
										{
											if ($_SESSION[$menu['table'].'_search_value_annee'] == $value) $nfselect = 'selected'; else $nfselect = '';
											echo '<option value="'.$value.'" '.$nfselect.'>'.$value.'</option>';
										}
									?>
								</select>
								<?php if (!empty($_SESSION[$menu['table'].'_search_value_annee'])) { ?>
									<div class="input-group-btn"><button type="submit" class="btn btn-primary" name="<?php echo $menu['table']; ?>_search_value_annee"><i class="fa fa-close"></i></button></div>
								<?php } ?>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</nav>
<?php
	if (!empty($_SESSION[$menu['table'].'_search_value']) || !empty($_SESSION[$menu['table'].'_search_value_support']) || !empty($_SESSION[$menu['table'].'_search_value_filmvu']) || !empty($_SESSION[$menu['table'].'_search_value_genre']) || !empty($_SESSION[$menu['table'].'_search_value_annee']))
	{
		if ($list_search_total > 0)
		{
			echo '<div class="alert alert-success">';
			echo '<strong>'.$list_search_total.'</strong> résultat(s)';
			echo '</div>';
		} else {
			echo '<div class="alert alert-danger">';
			echo '<strong>0</strong> résultat';
			echo '</div>';
		}
	}
?>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
			<div class="row">
				<div class="col-xs-12 col-sm-4 col-md-4">
					<div class="form-group">
						<label>Trier par</label>
						<form method="POST">
							<select class="form-control select2" name="option_order" onchange="this.form.submit()" style="width:100%;">
								<option value="TitreVF" <?php if ($option_order == 'TitreVF') echo 'selected'; ?>>Titre</option>
								<option value="TitreVF DESC" <?php if ($option_order == 'TitreVF DESC') echo 'selected'; ?>>Titre (desc)</option>
								<option value="Annee" <?php if ($option_order == 'Annee') echo 'selected'; ?>>Année</option>
								<option value="Annee DESC" <?php if ($option_order == 'Annee DESC') echo 'selected'; ?>>Année (desc)</option>
								<option value="EntreeDate" <?php if ($option_order == 'EntreeDate') echo 'selected'; ?>>Date d'ajout</option>
								<option value="EntreeDate DESC" <?php if ($option_order == 'EntreeDate DESC') echo 'selected'; ?>>Date d'ajout (desc)</option>
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
			</div>
		</div>
	</div>
</nav>
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