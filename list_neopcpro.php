<?php
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach($_ as $key=>&$val)
	{
		Functions::secure($val);
	}
	
	/*
		=================================
		REGLAGE
		=================================
	*/
	$order_array = array('TitreVF', 'TitreVF DESC', 'Annee', 'Annee DESC', 'EntreeDate', 'EntreeDate DESC');
	if (!isset($_SESSION['option_order'])) $_SESSION['option_order'] = 'TitreVF';
	if (isset($_POST['option_order']) && in_array($_POST['option_order'], $order_array)) $_SESSION['option_order'] = $_POST['option_order'];
	$option_order = $_SESSION['option_order'];
	
	$nb_elements_array = array('6', '12', '18', '24', '30', '36');
	if (!isset($_SESSION['option_nb_elements'])) $_SESSION['option_nb_elements'] = '24';
	if (isset($_POST['option_nb_elements']) && in_array($_POST['option_nb_elements'], $nb_elements_array)) $_SESSION['option_nb_elements'] = $_POST['option_nb_elements'];
	$option_nb_elements = $_SESSION['option_nb_elements'];

	$dp_type_array = array('liste', 'galerie', 'table');
	if (!isset($_SESSION['option_dp_type'])) $_SESSION['option_dp_type'] = 'galerie';
	if (isset($_POST['option_dp_type']) && in_array($_POST['option_dp_type'], $dp_type_array)) $_SESSION['option_dp_type'] = $_POST['option_dp_type'];
	$option_dp_type = $_SESSION['option_dp_type'];
	
	$menu_query = $db->prepare('SELECT `id`, `table`, `type` FROM site_menu WHERE `id` = "'.$table.'"');
	$menu_query->execute();
	$menu = $menu_query->fetch();
	$menu_query->closeCursor();
	
	/*
		=================================
		LISTE
		=================================
	*/
	// Total
	$query = $db->prepare('SELECT COUNT(ID) FROM '.$menu['table']);
	$query->execute();
	$list_total = $query->fetchColumn();
	$query->closeCursor();
	
	// Page
	if (isset($_['page']) && is_numeric($_['page']))
	{
		if ($_['page'] >= 1 && $_['page'] <= ceil($list_total/$option_nb_elements)) $page = intval($_['page']);
		else $page = 1;
	}
	else
	{
		$page = 1;
	}
	$offset_list = ($page-1) * $option_nb_elements;
		
	$list_search = NULL;
	
	// Recherche + Filtres
	if (!isset($_SESSION[$menu['table'].'_search_label']))
	{
		$_SESSION[$menu['table'].'_search_label'] = 'TitreVF';
		$_SESSION[$menu['table'].'_search_value'] = NULL;
	}
	if (!isset($_SESSION[$menu['table'].'_search_label_support']))
	{
		$_SESSION[$menu['table'].'_search_label_support'] = 'Support';
		$_SESSION[$menu['table'].'_search_value_support'] = NULL;
	}
	if (!isset($_SESSION[$menu['table'].'_search_label_edition']))
	{
		$_SESSION[$menu['table'].'_search_label_edition'] = 'Edition';
		$_SESSION[$menu['table'].'_search_value_edition'] = NULL;
	}
	if (!isset($_SESSION[$menu['table'].'_search_label_filmvu']))
	{
		$_SESSION[$menu['table'].'_search_label_filmvu'] = 'FilmVu';
		$_SESSION[$menu['table'].'_search_value_filmvu'] = NULL;
	}
	if (!isset($_SESSION[$menu['table'].'_search_label_genre']))
	{
		$_SESSION[$menu['table'].'_search_label_genre'] = 'Genre';
		$_SESSION[$menu['table'].'_search_value_genre'] = NULL;
	}
	if (!isset($_SESSION[$menu['table'].'_search_label_annee']))
	{
		$_SESSION[$menu['table'].'_search_label_annee'] = 'Annee';
		$_SESSION[$menu['table'].'_search_value_annee'] = NULL;
	}
	
	if (isset($_[$menu['table'].'_search_value']))
	{
		$_SESSION[$menu['table'].'_search_label'] = $_[$menu['table'].'_search_label'];
		$_SESSION[$menu['table'].'_search_value'] = $_[$menu['table'].'_search_value'];
	}
	if (isset($_[$menu['table'].'_search_value_support']))
	{
		$_SESSION[$menu['table'].'_search_value_support'] = $_[$menu['table'].'_search_value_support'];
	}
	if (isset($_[$menu['table'].'_search_value_edition']))
	{
		$_SESSION[$menu['table'].'_search_value_edition'] = $_[$menu['table'].'_search_value_edition'];
	}
	if (isset($_[$menu['table'].'_search_value_filmvu']))
	{
		$_SESSION[$menu['table'].'_search_value_filmvu'] = $_[$menu['table'].'_search_value_filmvu'];
	}
	if (isset($_[$menu['table'].'_search_value_genre']))
	{
		$_SESSION[$menu['table'].'_search_value_genre'] = $_[$menu['table'].'_search_value_genre'];
	}
	if (isset($_[$menu['table'].'_search_value_annee']))
	{
		$_SESSION[$menu['table'].'_search_value_annee'] = $_[$menu['table'].'_search_value_annee'];
	}

	if ($_SESSION[$menu['table'].'_search_value'] != NULL || $_SESSION[$menu['table'].'_search_value_support'] || $_SESSION[$menu['table'].'_search_value_edition'] != NULL || $_SESSION[$menu['table'].'_search_value_filmvu'] != NULL || $_SESSION[$menu['table'].'_search_value_genre'] != NULL || $_SESSION[$menu['table'].'_search_value_annee'] != NULL)
	{
		if ($_SESSION[$menu['table'].'_search_value'] != NULL) $list_search .= ' AND '.$_SESSION[$menu['table'].'_search_label'].' LIKE "%'.$_SESSION[$menu['table'].'_search_value'].'%"';
		if ($_SESSION[$menu['table'].'_search_value_support'] != NULL) $list_search .= ' AND '.$_SESSION[$menu['table'].'_search_label_support'].' = "'.$_SESSION[$menu['table'].'_search_value_support'].'"';
		if ($_SESSION[$menu['table'].'_search_value_edition'] != NULL) $list_search .= ' AND '.$_SESSION[$menu['table'].'_search_label_edition'].' LIKE "%'.$_SESSION[$menu['table'].'_search_value_edition'].'%"';
		if ($_SESSION[$menu['table'].'_search_value_filmvu'] != NULL) $list_search .= ' AND '.$_SESSION[$menu['table'].'_search_label_filmvu'].' = "'.$_SESSION[$menu['table'].'_search_value_filmvu'].'"';
		if ($_SESSION[$menu['table'].'_search_value_genre'] != NULL) $list_search .= ' AND '.$_SESSION[$menu['table'].'_search_label_genre'].' LIKE "%'.$_SESSION[$menu['table'].'_search_value_genre'].'%"';
		if ($_SESSION[$menu['table'].'_search_value_annee'] != NULL) $list_search .= ' AND '.$_SESSION[$menu['table'].'_search_label_annee'].' = "'.$_SESSION[$menu['table'].'_search_value_annee'].'"';
	}
	
	$query = $db->prepare('SELECT COUNT(ID) FROM '.$menu['table'].' WHERE Sortie="NON" '.$list_search);
	$query->execute();
	$list_search_total = $query->fetchColumn();
	$query->closeCursor();
	
	// Liste
	$listing_query = $db->prepare('SELECT * FROM '.$menu['table'].' WHERE `Note` >= "0" '.$list_search.' ORDER BY '.$option_order.' LIMIT '.$option_nb_elements.' OFFSET '.$offset_list);
	$listing_query->execute();

	// Liste par support
	$query = $db->prepare('SELECT distinct Support FROM '.$menu['table'].' WHERE `Note` >= "0" '.$list_search);
	$query->execute();
	$i=0;
	$tempo_list = array();
	while ($nf_list = $query->fetch())
	{
		$unique_list = array($nf_list['Support']);
		foreach ($unique_list as $key => $value)
		{
			$unique_list2 = explode(" - ", $value);
			foreach ($unique_list2 as $key => $value)
			{
				$tempo_list[$i] = $value;
				$i++;
			}
		}
	}
	$query->closeCursor();
	$list_support = array_unique($tempo_list);
	sort($list_support);

	// Liste par édition
	$query = $db->prepare('SELECT distinct Edition FROM '.$menu['table'].' WHERE `Note` >= "0" '.$list_search);
	$query->execute();
	$i=0;
	$tempo_list = array();
	while ($nf_list = $query->fetch())
	{
		$unique_list = array($nf_list['Edition']);
		foreach ($unique_list as $key => $value)
		{
			$unique_list2 = explode(" - ", $value);
			foreach ($unique_list2 as $key => $value)
			{
				$tempo_list[$i] = $value;
				$i++;
			}
		}
	}
	$query->closeCursor();
	$list_edition = array_unique($tempo_list);
	sort($list_edition);

	// Liste par film vu
	$query = $db->prepare('SELECT distinct FilmVu FROM '.$menu['table'].' WHERE `Note` >= "0" '.$list_search);
	$query->execute();
	$i=0;
	$tempo_list = array();
	while ($nf_list = $query->fetch())
	{
		$unique_list = array($nf_list['FilmVu']);
		foreach ($unique_list as $key => $value)
		{
			$unique_list2 = explode(" - ", $value);
			foreach ($unique_list2 as $key => $value)
			{
				$tempo_list[$i] = $value;
				$i++;
			}
		}
	}
	$query->closeCursor();
	$list_filmvu = array_unique($tempo_list);
	sort($list_filmvu);

	// Liste par genre
	$query = $db->prepare('SELECT distinct Genre FROM '.$menu['table'].' WHERE `Note` >= "0" '.$list_search);
	$query->execute();
	$i=0;
	$tempo_list = array();
	while ($nf_list = $query->fetch())
	{
		$unique_list = array();
		$unique_list = explode(" / ", $nf_list['Genre']);
		foreach ($unique_list as $key => $value)
		{
			$unique_list2 = explode(" - ", $value);
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
	$query = $db->prepare('SELECT distinct Annee FROM '.$menu['table'].' WHERE `Note` >= "0" '.$list_search);
	$query->execute();
	$i=0;
	$tempo_list = array();
	while ($nf_list = $query->fetch())
	{
		$unique_list = array($nf_list['Annee']);
		foreach ($unique_list as $key => $value)
		{
			$unique_list2 = explode(" - ", $value);
			foreach ($unique_list2 as $key => $value)
			{
				$tempo_list[$i] = $value;
				$i++;
			}
		}
	}
	$query->closeCursor();
	$list_annee = array_unique($tempo_list);
	sort($list_annee);
?>
<script>document.title += ' - Liste'</script>
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
				<?php if ($menu['type'] == 'autre') { ?>
					<div class="col-xs-12 col-sm-4 col-md-6">
						<div class="form-group">
							<label>Recherche</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<input type="hidden" name="<?php echo $menu['table']; ?>_search_label" value="<?php if ($_SESSION[$menu['table'].'_search_label'] == 'TitreVF') echo 'TitreVF'; ?>" class="champ_recherche" />
								<div class="input-group">
									<div class="input-group-btn">
										<button type="submit" class="btn btn-success drop-toggle"><?php if ($_SESSION[$menu['table'].'_search_label'] == 'TitreVF') echo 'Titre'; ?></button>
									</div>
									<input type="text" name="<?php echo $menu['table']; ?>_search_value" value="<?php echo $_SESSION[$menu['table'].'_search_value']; ?>" class="form-control" id="autocomplete" />
								</div>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-6">
						<div class="form-group">
							<label>Filtrer par année</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="<?php echo $menu['table']; ?>_search_value_annee" onchange="this.form.submit()" class="form-control select2-annee" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_annee as $key => $value2)
										{
											if ($_SESSION[$menu['table'].'_search_value_annee'] == $value2) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value2.'" '.$nfselect.'>'.$value2.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
				<?php } ?>
				<?php if ($menu['type'] == 'livre') { ?>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Recherche</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<input type="hidden" name="<?php echo $menu['table']; ?>_search_label" value="<?php if ($_SESSION[$menu['table'].'_search_label'] == 'TitreVF') echo 'TitreVF'; ?><?php if ($_SESSION[$menu['table'].'_search_label'] == 'Realisateurs') echo 'Realisateurs'; ?>" class="champ_recherche" />
								<div class="input-group">
									<div class="input-group-btn">
										<button type="submit" class="btn btn-success drop-toggle"><?php if ($_SESSION[$menu['table'].'_search_label'] == 'TitreVF') echo 'Titre'; ?><?php if ($_SESSION[$menu['table'].'_search_label'] == 'Realisateurs') echo 'Auteurs'; ?></button>
										<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<ul class="dropdown-menu drop-menu">
											<li><a href="#" name="TitreVF">Titre</a></li>
											<li><a href="#" name="Realisateurs">Auteurs</a></li>
										</ul>
									</div>
									<input type="text" name="<?php echo $menu['table']; ?>_search_value" value="<?php echo $_SESSION[$menu['table'].'_search_value']; ?>" class="form-control" id="autocomplete" />
								</div>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Filtrer par genre</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="<?php echo $menu['table']; ?>_search_value_genre" onchange="this.form.submit()" class="form-control select2-genre" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_genre as $key => $value1)
										{
											if ($_SESSION[$menu['table'].'_search_value_genre'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value1.'" '.$nfselect.'>'.$value1.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Filtrer par année</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="<?php echo $menu['table']; ?>_search_value_annee" onchange="this.form.submit()" class="form-control select2-annee" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_annee as $key => $value2)
										{
											if ($_SESSION[$menu['table'].'_search_value_annee'] == $value2) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value2.'" '.$nfselect.'>'.$value2.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
				<?php } ?>
				<?php if ($menu['type'] == 'musique') { ?>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Recherche</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<input type="hidden" name="<?php echo $menu['table']; ?>_search_label" value="<?php if ($_SESSION[$menu['table'].'_search_label'] == 'TitreVF') echo 'TitreVF'; ?><?php if ($_SESSION[$menu['table'].'_search_label'] == 'Realisateurs') echo 'Realisateurs'; ?>" class="champ_recherche" />
								<div class="input-group">
									<div class="input-group-btn">
										<button type="submit" class="btn btn-success drop-toggle"><?php if ($_SESSION[$menu['table'].'_search_label'] == 'TitreVF') echo 'Titre'; ?><?php if ($_SESSION[$menu['table'].'_search_label'] == 'Realisateurs') echo 'Artistes / Groupe'; ?></button>
										<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<ul class="dropdown-menu drop-menu">
											<li><a href="#" name="TitreVF">Titre</a></li>
											<li><a href="#" name="Realisateurs">Artistes / Groupe</a></li>
										</ul>
									</div>
									<input type="text" name="<?php echo $menu['table']; ?>_search_value" value="<?php echo $_SESSION[$menu['table'].'_search_value']; ?>" class="form-control" id="autocomplete" />
								</div>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Filtrer par genre</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="<?php echo $menu['table']; ?>_search_value_genre" onchange="this.form.submit()" class="form-control select2-genre" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_genre as $key => $value1)
										{
											if ($_SESSION[$menu['table'].'_search_value_genre'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value1.'" '.$nfselect.'>'.$value1.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Filtrer par année</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="<?php echo $menu['table']; ?>_search_value_annee" onchange="this.form.submit()" class="form-control select2-annee" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_annee as $key => $value2)
										{
											if ($_SESSION[$menu['table'].'_search_value_annee'] == $value2) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value2.'" '.$nfselect.'>'.$value2.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
				<?php } ?>
				<?php if ($menu['type'] == 'video') { ?>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<label>Recherche</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<input type="hidden" name="<?php echo $menu['table']; ?>_search_label" value="<?php if ($_SESSION[$menu['table'].'_search_label'] == 'TitreVF') echo 'TitreVF'; ?><?php if ($_SESSION[$menu['table'].'_search_label'] == 'Acteurs') echo 'Acteurs'; ?><?php if ($_SESSION[$menu['table'].'_search_label'] == 'Realisateurs') echo 'Realisateurs'; ?>" class="champ_recherche" />
								<div class="input-group">
									<div class="input-group-btn">
										<button type="submit" class="btn btn-success drop-toggle"><?php if ($_SESSION[$menu['table'].'_search_label'] == 'TitreVF') echo 'Titre'; ?><?php if ($_SESSION[$menu['table'].'_search_label'] == 'Acteurs') echo 'Acteurs'; ?><?php if ($_SESSION[$menu['table'].'_search_label'] == 'Realisateurs') echo 'Réalisateurs'; ?></button>
										<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<ul class="dropdown-menu drop-menu">
											<li><a href="#" name="TitreVF">Titre</a></li>
											<li><a href="#" name="Acteurs">Acteurs</a></li>
											<li><a href="#" name="Realisateurs">Réalisateurs</a></li>
										</ul>
									</div>
									<input type="text" name="<?php echo $menu['table']; ?>_search_value" value="<?php echo $_SESSION[$menu['table'].'_search_value']; ?>" class="form-control" id="autocomplete" />
								</div>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-2">
						<div class="form-group">
							<label>Filtrer par support</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="<?php echo $menu['table']; ?>_search_value_support" onchange="this.form.submit()" class="form-control select2-support" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_support as $key => $value1)
										{
											if ($_SESSION[$menu['table'].'_search_value_support'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value1.'" '.$nfselect.'>'.$value1.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-2">
						<div class="form-group">
							<label>Filtrer par édition</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="<?php echo $menu['table']; ?>_search_value_edition" onchange="this.form.submit()" class="form-control select2-edition" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_edition as $key => $value1)
										{
											if ($_SESSION[$menu['table'].'_search_value_edition'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value1.'" '.$nfselect.'>'.$value1.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-2">
						<div class="form-group">
							<label>Filtrer par vu/non vu</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="<?php echo $menu['table']; ?>_search_value_filmvu" onchange="this.form.submit()" class="form-control select2-filmvu" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_filmvu as $key => $value1)
										{
											if ($_SESSION[$menu['table'].'_search_value_filmvu'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value1.'" '.$nfselect.'>'.$value1.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-2">
						<div class="form-group">
							<label>Filtrer par genre</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="<?php echo $menu['table']; ?>_search_value_genre" onchange="this.form.submit()" class="form-control select2-genre" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_genre as $key => $value1)
										{
											if ($_SESSION[$menu['table'].'_search_value_genre'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value1.'" '.$nfselect.'>'.$value1.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-2">
						<div class="form-group">
							<label>Filtrer par année</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="<?php echo $menu['table']; ?>_search_value_annee" onchange="this.form.submit()" class="form-control select2-annee" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_annee as $key => $value2)
										{
											if ($_SESSION[$menu['table'].'_search_value_annee'] == $value2) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value2.'" '.$nfselect.'>'.$value2.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</nav>
<?php
	if ($_SESSION[$menu['table'].'_search_value'] != NULL || $_SESSION[$menu['table'].'_search_value_support'] != NULL || $_SESSION[$menu['table'].'_search_value_edition'] != NULL || $_SESSION[$menu['table'].'_search_value_filmvu'] != NULL || $_SESSION[$menu['table'].'_search_value_genre'] != NULL || $_SESSION[$menu['table'].'_search_value_annee'] != NULL)
	{
		if ($list_search_total != '0')
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
<nav class="text-center">
	<ul class="pagination">
		<?php
			$total 		= $list_search_total;			// nombre d'entrées dans la table
			$epp 		= $option_nb_elements; 			// nombre d'entrées à afficher par page
			$nbPages 	= ceil($total/$epp); 			// calcul du nombre de pages $nbPages (on arrondit à l'entier supérieur avec la fonction ceil())
			// Récupération du numéro de la page courante depuis l'URL avec la méthode GET
			// S'il s'agit d'un nombre on traite, sinon on garde la valeur par défaut : 1
			$current = 1;
			if (isset($_GET['page']) && is_numeric($_GET['page']))
			{
				$page = intval($_GET['page']);
				if ($page >= 1 && $page <= $nbPages) $current = $page;
				else if ($page < 1) $current = 1;
				else $current = 1;
			}
			echo paginate('?op=list&table='.$table.'', '&page=', $nbPages, $current);
		?>
	</ul>
</nav>
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
							<select name="option_order" onchange="this.form.submit()" class="form-control select2">
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
							<select name="option_nb_elements" onchange="this.form.submit()" class="form-control select2">
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
							<select name="option_dp_type" onchange="this.form.submit()" class="form-control select2">
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
<div class="panel panel-default">
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
							<?php $filename = sprintf('profils/'.$menu['table'].'/affiches/Filmotech_%05d.jpg', $listing['ID']); ?>
							<?php if (file_exists($filename)) echo '<div class="list"><img data-original="'.$filename.'" class="list-img lazy" alt="affiche" /></div>'; else echo '<div class="list"><img data-src="holder.js/100px165?text=aucune \n image" alt="affiche" /></div>'; ?>
						</td>
						<td>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-lg-12"><span class="text-info"><?php echo $listing['TitreVF']; ?></span></div>
							</div>
							<div class="row">
								<?php if($menu['type'] == 'livre') $realisateurs = 'Auteur(s)'; if($menu['type'] == 'musique') $realisateurs = 'Artiste / Groupe'; if($menu['type'] == 'video') $realisateurs = 'Réalisateur(s)'; ?>
								<div class="col-xs-4 col-sm-4 col-lg-2"><strong><?php echo $realisateurs; ?></strong></div>
								<div class="col-xs-8 col-sm-8 col-lg-10"><span class="text-danger"><?php echo str_replace("\r", " / ", $listing['Realisateurs']); ?></span></div>
							</div>
							<div class="row">
								<div class="col-xs-4 col-sm-4 col-lg-2"><strong>Genre</strong></div>
								<div class="col-xs-8 col-sm-8 col-lg-10"><span class="text-danger"><?php echo $listing['Genre']; ?></span></div>
							</div>
							<div class="row">
								<div class="col-xs-4 col-sm-4 col-lg-2"><strong>Année</strong></div>
								<div class="col-xs-8 col-sm-8 col-lg-10"><span class="text-danger"><?php echo $listing['Annee']; ?></span></div>
							</div>
							<div class="row">
								<?php if ($listing['Duree'] > '0') { if($menu['type'] == 'livre') $duree = $listing['Duree'].' pages'; if($menu['type'] == 'musique' || $menu['type'] == 'video') $duree = floor($listing['Duree']/60).'h '.($listing['Duree']%60).'min'; } else { $duree = ''; } ?>
								<div class="col-xs-4 col-sm-4 col-lg-2"><strong><?php if($menu['type'] == 'livre') echo 'pages'; if($menu['type'] == 'musique' || $menu['type'] == 'video') echo 'Durée'; ?></strong></div>
								<div class="col-xs-8 col-sm-8 col-lg-10"><span class="text-danger"><?php echo $duree; ?></span></div>
							</div>
							<?php if($menu['type'] == 'video') { ?>
								<div class="row">
									<div class="col-xs-4 col-sm-4 col-lg-2"><strong>Note</strong></div>
									<div class="col-xs-8 col-sm-8 col-lg-10"><span class="text-danger"><img src="img/note<?php echo $listing['Note']; ?>.png" /></span></div>
								</div>
							<?php } ?>
						</td>
					</tr>
				<?php } $listing_query->closeCursor(); ?>
			</tbody>
		</table>
	<?php } ?>
	<?php if ($option_dp_type == 'galerie') { ?>
		<div class="panel-body">
			<div class="row text-center">
				<?php while ($listing = $listing_query->fetch()) { ?>
					<div class="col-xs-6 col-sm-4 col-md-2">
						<div class="thumbnail">
							<?php $filename = sprintf('profils/'.$menu['table'].'/affiches/Filmotech_%05d.jpg', $listing['ID']); ?>
							<?php if (file_exists($filename)) echo '<div class="list"><img data-original="'.$filename.'" class="list-img lazy" alt="affiche" /></div>'; else echo '<div class="list"><img data-src="holder.js/100px165?text=aucune \n image" alt="affiche" /></div>'; ?>
							<div class="list-year text-danger"><?php echo $listing['Annee']; ?></div>
							<div class="list-title text-info"><?php echo $listing['TitreVF']; ?></div>
						</div>
					</div>
				<?php } $listing_query->closeCursor(); ?>
			</div>
		</div>
	<?php } ?>
	<?php if ($option_dp_type == 'table') { ?>
		<div class="table-responsive">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Titre</th>
						<?php if($menu['type'] == 'video') echo '<th style="width:9%">Support</th>'; ?>
						<th style="width:15%">Genre</th>
						<th style="width:9%">Année</th>
						<th style="width:9%"><?php if($menu['type'] == 'livre') echo 'pages'; if($menu['type'] == 'musique' || $menu['type'] == 'video') echo 'Durée'; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php while ($listing = $listing_query->fetch()) { ?>
						<tr>
							<td><?php echo $listing['TitreVF']; ?></td>
							<?php if($menu['type'] == 'video') echo '<td>'.$listing['Support'].'</td>'; ?>
							<td><?php echo $listing['Genre']; ?></td>
							<td><?php echo $listing['Annee']; ?></td>
							<td><?php if ($listing['Duree'] > '0') { if($menu['type'] == 'livre') echo $listing['Duree']; if($menu['type'] == 'musique' || $menu['type'] == 'video') echo floor($listing['Duree']/60).'h '.($listing['Duree']%60).'min'; } ?></td>
						</tr>
					<?php } $listing_query->closeCursor(); ?>
				</tbody>
			</table>
		</div>
	<?php } ?>
</div>
<nav class="text-center">
	<ul class="pagination">
		<?php
			$total 		= $list_search_total;			// nombre d'entrées dans la table
			$epp 		= $option_nb_elements; 			// nombre d'entrées à afficher par page
			$nbPages 	= ceil($total/$epp); 			// calcul du nombre de pages $nbPages (on arrondit à l'entier supérieur avec la fonction ceil())
			// Récupération du numéro de la page courante depuis l'URL avec la méthode GET
			// S'il s'agit d'un nombre on traite, sinon on garde la valeur par défaut : 1
			$current = 1;
			if (isset($_GET['page']) && is_numeric($_GET['page']))
			{
				$page = intval($_GET['page']);
				if ($page >= 1 && $page <= $nbPages) $current = $page;
				else if ($page < 1) $current = 1;
				else $current = 1;
			}
			echo paginate('?op=list&table='.$table.'', '&page=', $nbPages, $current);
		?>
	</ul>
</nav>