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
	
	require_once('./profils/'.$menu['table'].'/include/config.inc.php');
	$cfg = new CONFIG();
	try
	{
		$db_list = new PDO($cfg->DB_TYPE.':host='.$cfg->DB_SERVER.';dbname='.$cfg->DB_NAME, $cfg->DB_USER, $cfg->DB_PASSWORD);
		$db_list->query('SET NAMES UTF8');
	}
	catch (Exception $e)
	{
		die('Erreur : '.$e->getMessage());
	}
	
	// Total
	$query = $db_list->prepare('SELECT COUNT(ID) FROM '.$cfg->DB_TABLE);
	$query->execute();
	$list_total = $query->fetchColumn();
	$query->closeCursor();
	
	// Page
	if (isset($_GET['page']) && is_numeric($_GET['page']))
	{
		if ($_GET['page'] >= 1 && $_GET['page'] <= ceil($list_total/$option_nb_elements)) $page = intval($_GET['page']);
		else $page = 1;
	}
	else
	{
		$page = 1;
	}
	$offset_list = ($page-1) * $option_nb_elements;
		
	$list_search = NULL;
	
	if (!isset($_SESSION['livre_search_label'])) { $_SESSION['livre_search_label'] = 'TitreVF'; $_SESSION['livre_search_value'] = NULL; }
	if (!isset($_SESSION['livre_search_label_genre'])) { $_SESSION['livre_search_label_genre'] = NULL; $_SESSION['livre_search_value_genre'] = NULL; }
	if (!isset($_SESSION['livre_search_label_annee'])) { $_SESSION['livre_search_label_annee']= NULL; $_SESSION['livre_search_value_annee'] = NULL; }
	
	if (!isset($_SESSION['musique_search_label'])) { $_SESSION['musique_search_label'] = 'TitreVF'; $_SESSION['musique_search_value'] = NULL; }
	if (!isset($_SESSION['musique_search_label_genre'])) { $_SESSION['musique_search_label_genre'] = NULL; $_SESSION['musique_search_value_genre'] = NULL; }
	if (!isset($_SESSION['musique_search_label_annee'])) { $_SESSION['musique_search_label_annee']= NULL; $_SESSION['musique_search_value_annee'] = NULL; }
	
	if (!isset($_SESSION['video_search_label'])) { $_SESSION['video_search_label'] = 'TitreVF'; $_SESSION['video_search_value'] = NULL; }
	if (!isset($_SESSION['video_search_label_support'])) { $_SESSION['video_search_label_support'] = NULL; $_SESSION['video_search_value_support'] = NULL; }
	if (!isset($_SESSION['video_search_label_filmvu'])) { $_SESSION['video_search_label_filmvu'] = NULL; $_SESSION['video_search_value_filmvu'] = NULL; }
	if (!isset($_SESSION['video_search_label_genre'])) { $_SESSION['video_search_label_genre'] = NULL; $_SESSION['video_search_value_genre'] = NULL; }
	if (!isset($_SESSION['video_search_label_annee'])) { $_SESSION['video_search_label_annee']= NULL; $_SESSION['video_search_value_annee'] = NULL; }
	
	if ($menu['type'] == 'livre')
	{
		if (isset($_['livre_search_value'])) { $_SESSION['livre_search_label'] = $_['livre_search_label']; $_SESSION['livre_search_value'] = $_['livre_search_value']; }
		if (isset($_['livre_search_value_genre'])) { $_SESSION['livre_search_label_genre'] = 'Genre'; $_SESSION['livre_search_value_genre'] = $_['livre_search_value_genre']; }
		if (isset($_['livre_search_value_annee'])) { $_SESSION['livre_search_label_annee'] = 'Annee'; $_SESSION['livre_search_value_annee'] = $_['livre_search_value_annee']; }

		if ($_SESSION['livre_search_value'] != NULL || $_SESSION['livre_search_value_genre'] != NULL || $_SESSION['livre_search_value_annee'] != NULL)
		{
			if ($_SESSION['livre_search_value'] != NULL) $list_search .= ' AND '.$_SESSION['livre_search_label'].' LIKE "%'.$_SESSION['livre_search_value'].'%"';
			if ($_SESSION['livre_search_value_genre'] != NULL) $list_search .= ' AND '.$_SESSION['livre_search_label_genre'].' LIKE "%'.$_SESSION['livre_search_value_genre'].'%"';
			if ($_SESSION['livre_search_value_annee'] != NULL) $list_search .= ' AND '.$_SESSION['livre_search_label_annee'].' = "'.$_SESSION['livre_search_value_annee'].'"';
		}
	}
	
	if ($menu['type'] == 'musique')
	{
		if (isset($_['musique_search_value'])) { $_SESSION['musique_search_label'] = $_['musique_search_label']; $_SESSION['musique_search_value'] = $_['musique_search_value']; }
		if (isset($_['musique_search_value_genre'])) { $_SESSION['musique_search_label_genre'] = 'Genre'; $_SESSION['musique_search_value_genre'] = $_['musique_search_value_genre']; }
		if (isset($_['musique_search_value_annee'])) { $_SESSION['musique_search_label_annee'] = 'Annee'; $_SESSION['musique_search_value_annee'] = $_['musique_search_value_annee']; }

		if ($_SESSION['musique_search_value'] != NULL || $_SESSION['musique_search_value_genre'] != NULL || $_SESSION['musique_search_value_annee'] != NULL)
		{
			if ($_SESSION['musique_search_value'] != NULL) $list_search .= ' AND '.$_SESSION['musique_search_label'].' LIKE "%'.$_SESSION['musique_search_value'].'%"';
			if ($_SESSION['musique_search_value_genre'] != NULL) $list_search .= ' AND '.$_SESSION['musique_search_label_genre'].' = "'.$_SESSION['musique_search_value_genre'].'"';
			if ($_SESSION['musique_search_value_annee'] != NULL) $list_search .= ' AND '.$_SESSION['musique_search_label_annee'].' = "'.$_SESSION['musique_search_value_annee'].'"';
		}
	}
	
	if ($menu['type'] == 'video')
	{
		if (isset($_['video_search_value'])) { $_SESSION['video_search_label'] = $_['video_search_label']; $_SESSION['video_search_value'] = $_['video_search_value']; }
		if (isset($_['video_search_value_support'])) { $_SESSION['video_search_label_support'] = 'Support'; $_SESSION['video_search_value_support'] = $_['video_search_value_support']; }
		if (isset($_['video_search_value_filmvu'])) { $_SESSION['video_search_label_filmvu'] = 'FilmVu'; $_SESSION['video_search_value_filmvu'] = $_['video_search_value_filmvu']; }
		if (isset($_['video_search_value_genre'])) { $_SESSION['video_search_label_genre'] = 'Genre'; $_SESSION['video_search_value_genre'] = $_['video_search_value_genre']; }
		if (isset($_['video_search_value_annee'])) { $_SESSION['video_search_label_annee'] = 'Annee'; $_SESSION['video_search_value_annee'] = $_['video_search_value_annee']; }

		if ($_SESSION['video_search_value'] != NULL || $_SESSION['video_search_value_support'] != NULL || $_SESSION['video_search_value_filmvu'] != NULL || $_SESSION['video_search_value_genre'] != NULL || $_SESSION['video_search_value_annee'] != NULL)
		{
			if ($_SESSION['video_search_value'] != NULL) $list_search .= ' AND '.$_SESSION['video_search_label'].' LIKE "%'.$_SESSION['video_search_value'].'%"';
			if ($_SESSION['video_search_value_support'] != NULL) $list_search .= ' AND '.$_SESSION['video_search_label_support'].' = "'.$_SESSION['video_search_value_support'].'"';
			if ($_SESSION['video_search_value_filmvu'] != NULL) $list_search .= ' AND '.$_SESSION['video_search_label_filmvu'].' = "'.$_SESSION['video_search_value_filmvu'].'"';
			if ($_SESSION['video_search_value_genre'] != NULL) $list_search .= ' AND '.$_SESSION['video_search_label_genre'].' = "'.$_SESSION['video_search_value_genre'].'"';
			if ($_SESSION['video_search_value_annee'] != NULL) $list_search .= ' AND '.$_SESSION['video_search_label_annee'].' = "'.$_SESSION['video_search_value_annee'].'"';
		}
	}
	
	$query = $db->prepare('SELECT COUNT(ID) FROM '.$cfg->DB_TABLE.' WHERE Sortie="NON" '.$list_search);
	$query->execute();
	$list_search_total = $query->fetchColumn();
	$query->closeCursor();
		
	$listing_query = $db->prepare('SELECT * FROM '.$cfg->DB_TABLE.' WHERE `Sortie` = "NON" '.$list_search.' ORDER BY '.$option_order.' LIMIT '.$option_nb_elements.' OFFSET '.$offset_list);
	$listing_query->execute();

	// Liste par support
	$query = $db->prepare('SELECT distinct Support FROM '.$cfg->DB_TABLE);
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

	// Liste par film vu
	$query = $db->prepare('SELECT distinct FilmVu FROM '.$cfg->DB_TABLE);
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
	$query = $db->prepare('SELECT distinct Genre FROM '.$cfg->DB_TABLE);
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
	$query = $db->prepare('SELECT distinct Annee FROM '.$cfg->DB_TABLE);
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
				<?php if ($menu['type'] == 'livre') { ?>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Recherche</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<input type="hidden" name="livre_search_label" value="<?php if ($_SESSION['livre_search_label'] == 'TitreVF') echo 'TitreVF'; ?><?php if ($_SESSION['livre_search_label'] == 'Realisateurs') echo 'Realisateurs'; ?>" class="champ_recherche" />
								<div class="input-group">
									<div class="input-group-btn">
										<button type="submit" class="btn btn-success drop-toggle"><?php if ($_SESSION['livre_search_label'] == 'TitreVF') echo 'Titre'; ?><?php if ($_SESSION['livre_search_label'] == 'Realisateurs') echo 'Auteurs'; ?></button>
										<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<ul class="dropdown-menu drop-menu">
											<li><a href="#" name="TitreVF">Titre</a></li>
											<li><a href="#" name="Realisateurs">Auteurs</a></li>
										</ul>
									</div>
									<input type="text" name="livre_search_value" value="<?php echo $_SESSION['livre_search_value']; ?>" class="form-control" />
								</div>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Filtrer par genre</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="livre_search_value_genre" onchange="this.form.submit()" class="form-control select2-genre" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_genre as $key => $value1)
										{
											if ($_SESSION['livre_search_value_genre'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
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
								<select name="livre_search_value_annee" onchange="this.form.submit()" class="form-control select2-annee" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_annee as $key => $value2)
										{
											if ($_SESSION['livre_search_value_annee'] == $value2) $nfselect = 'selected'; else $nfselect = NULL;
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
								<input type="hidden" name="musique_search_label" value="<?php if ($_SESSION['musique_search_label'] == 'TitreVF') echo 'TitreVF'; ?><?php if ($_SESSION['musique_search_label'] == 'Realisateurs') echo 'Realisateurs'; ?>" class="champ_recherche" />
								<div class="input-group">
									<div class="input-group-btn">
										<button type="submit" class="btn btn-success drop-toggle"><?php if ($_SESSION['musique_search_label'] == 'TitreVF') echo 'Titre'; ?><?php if ($_SESSION['musique_search_label'] == 'Realisateurs') echo 'Artistes / Groupe'; ?></button>
										<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<ul class="dropdown-menu drop-menu">
											<li><a href="#" name="TitreVF">Titre</a></li>
											<li><a href="#" name="Realisateurs">Artistes / Groupe</a></li>
										</ul>
									</div>
									<input type="text" name="musique_search_value" value="<?php echo $_SESSION['musique_search_value']; ?>" class="form-control" />
								</div>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Filtrer par genre</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="musique_search_value_genre" onchange="this.form.submit()" class="form-control select2-genre" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_genre as $key => $value1)
										{
											if ($_SESSION['musique_search_value_genre'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
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
								<select name="musique_search_value_annee" onchange="this.form.submit()" class="form-control select2-annee" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_annee as $key => $value2)
										{
											if ($_SESSION['musique_search_value_annee'] == $value2) $nfselect = 'selected'; else $nfselect = NULL;
											echo '<option value="'.$value2.'" '.$nfselect.'>'.$value2.'</option>';
										}
									?>
								</select>
							</form>
						</div>
					</div>
				<?php } ?>
				<?php if ($menu['type'] == 'video') { ?>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="form-group">
							<label>Recherche</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<input type="hidden" name="video_search_label" value="<?php if ($_SESSION['video_search_label'] == 'TitreVF') echo 'TitreVF'; ?><?php if ($_SESSION['video_search_label'] == 'Acteurs') echo 'Acteurs'; ?><?php if ($_SESSION['video_search_label'] == 'Realisateurs') echo 'Realisateurs'; ?>" class="champ_recherche" />
								<div class="input-group">
									<div class="input-group-btn">
										<button type="submit" class="btn btn-success drop-toggle"><?php if ($_SESSION['video_search_label'] == 'TitreVF') echo 'Titre'; ?><?php if ($_SESSION['video_search_label'] == 'Acteurs') echo 'Acteurs'; ?><?php if ($_SESSION['video_search_label'] == 'Realisateurs') echo 'Réalisateurs'; ?></button>
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
									<input type="text" name="video_search_value" value="<?php echo $_SESSION['video_search_value']; ?>" class="form-control" />
								</div>
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-2">
						<div class="form-group">
							<label>Filtrer par support</label>
							<form method="POST" action="?op=list&table=<?php echo $table; ?>">
								<select name="video_search_value_support" onchange="this.form.submit()" class="form-control select2-support" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_support as $key => $value1)
										{
											if ($_SESSION['video_search_value_support'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
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
								<select name="video_search_value_filmvu" onchange="this.form.submit()" class="form-control select2-filmvu" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_filmvu as $key => $value1)
										{
											if ($_SESSION['video_search_value_filmvu'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
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
								<select name="video_search_value_genre" onchange="this.form.submit()" class="form-control select2-genre" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_genre as $key => $value1)
										{
											if ($_SESSION['video_search_value_genre'] == $value1) $nfselect = 'selected'; else $nfselect = NULL;
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
								<select name="video_search_value_annee" onchange="this.form.submit()" class="form-control select2-annee" style="width:100%;">
									<option></option>
									<?php
										foreach ($list_annee as $key => $value2)
										{
											if ($_SESSION['video_search_value_annee'] == $value2) $nfselect = 'selected'; else $nfselect = NULL;
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
	if ($_SESSION['livre_search_value'] != NULL || $_SESSION['livre_search_value_genre'] != NULL || $_SESSION['livre_search_value_annee'] != NULL)
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
	
	if ($_SESSION['musique_search_value'] != NULL || $_SESSION['musique_search_value_genre'] != NULL || $_SESSION['musique_search_value_annee'] != NULL)
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
	
	if ($_SESSION['video_search_value'] != NULL || $_SESSION['video_search_value_support'] != NULL || $_SESSION['video_search_value_filmvu'] != NULL || $_SESSION['video_search_value_genre'] != NULL || $_SESSION['video_search_value_annee'] != NULL)
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
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
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
							<?php $filename = sprintf('profils/'.$cfg->DB_TABLE.'/'.$cfg->POSTERS_DIRECTORY.'/Filmotech_%05d.jpg', $listing['ID']); ?>
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
							<?php $filename = sprintf('profils/'.$cfg->DB_TABLE.'/'.$cfg->POSTERS_DIRECTORY.'/Filmotech_%05d.jpg', $listing['ID']); ?>
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