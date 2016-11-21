<?php
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key => &$val)
	{
		Functions::secure($val);
	}
	
	function paginate($url, $link, $total, $current, $adj=2)
	{
		// Initialisation des variables
		$prev = $current - 1; 			// numéro de la page précédente
		$next = $current + 1; 			// numéro de la page suivante
		$penultimate = $total - 1; 		// numéro de l'avant-dernière page
		$pagination = ''; 				// variable retour de la fonction : vide tant qu'il n'y a pas au moins 2 pages

		if ($total > 1)
		{
			// Remplissage de la chaîne de caractères à retourner
			$pagination .= "\n";

			/* =================================
			 *  Affichage du bouton [précédent]
			 * ================================= */
			if ($current == 2)
			{
				// la page courante est la 2, le bouton renvoie donc sur la page 1, remarquez qu'il est inutile de mettre $url{$link}1
				$pagination .= '<li><a href="'.$url.'"><i class="fa fa-angle-double-left"></i></a></li>';
			}
			elseif ($current > 2)
			{
				// la page courante est supérieure à 2, le bouton renvoie sur la page dont le numéro est immédiatement inférieur
				$pagination .= '<li><a href="'.$url.$link.$prev.'"><i class="fa fa-angle-double-left"></i></a></li>';
			} else {
				// dans tous les autres cas, la page est 1 : désactivation du bouton [précédent]
				$pagination .= '<li class="disabled"><a href="#"><i class="fa fa-angle-double-left"></i></a></li>';
			}

			/*
				* Début affichage des pages, l'exemple reprend le cas de 3 numéros de pages adjacents (par défaut) de chaque côté du numéro courant
				* - CAS 1 : il y a au plus 12 pages, insuffisant pour faire une troncature
				* - CAS 2 : il y a au moins 13 pages, on effectue la troncature pour afficher 11 numéros de pages au total
			*/

			/* ===============================================
			 *  CAS 1 : au plus 12 pages -> pas de troncature
			 * =============================================== */
			if ($total < 7 + ($adj * 2))
			{
				// Ajout de la page 1 : on la traite en dehors de la boucle pour n'avoir que index.php au lieu de index.php?p=1 et ainsi éviter le duplicate content
				$pagination .= ($current == 1) ? '<li class="active"><a href="#">1</a></li>' : '<li><a href="'.$url.'">1</a></li>'; // Opérateur ternaire : (condition) ? 'valeur si vrai' : 'valeur si fausse'

				// Pour les pages restantes on utilise itère
				for ($i=2; $i<=$total; $i++)
				{
					if ($i == $current)
					{
						// Le numéro de la page courante est mis en évidence
						$pagination .= '<li class="active"><a href="#">'.$i.'</a></li>';
					} else {
						// Les autres sont affichées normalement
						$pagination .= '<li><a href="'.$url.$link.$i.'">'.$i.'</a></li>';
					}
				}
			}

			/* =========================================
			 *  CAS 2 : au moins 13 pages -> troncature
			 * ========================================= */
			else {
				/*
					* Troncature 1 : on se situe dans la partie proche des premières pages, on tronque donc la fin de la pagination.
					* l'affichage sera de neuf numéros de pages à gauche ... deux à droite
					* 1 2 3 4 5 6 7 8 9 … 16 17
				*/
				if ($current < 2 + ($adj * 2))
				{
					// Affichage du numéro de page 1
					$pagination .= ($current == 1) ? '<li class="active"><a href="#">1</a></li>' : '<li><a href="' . $url . '">1</a></li>';

					// puis des huit autres suivants
					for ($i = 2; $i < 4 + ($adj * 2); $i++)
					{
						if ($i == $current)
						{
							$pagination .= '<li class="active"><a href="#">'.$i.'</a></li>';
						} else {
							$pagination .= '<li><a href="'.$url.$link.$i.'">'.$i.'</a>';
						}
					}

					// ... pour marquer la troncature
					$pagination .= '<li><a href="#">&hellip;</a></li>';

					// et enfin les deux derniers numéros
					$pagination .= '<li><a href="'.$url.$link.$penultimate.'">'.$penultimate.'</a></li>';
					$pagination .= '<li><a href="'.$url.$link.$total.'">'.$total.'</a></li>';
				}
				/*
					* Troncature 2 : on se situe dans la partie centrale de notre pagination, on tronque donc le début et la fin de la pagination.
					* l'affichage sera deux numéros de pages à gauche ... sept au centre ... deux à droite
					* 1 2 … 5 6 7 8 9 10 11 … 16 17
				*/
				elseif ((($adj * 2) + 1 < $current) && ($current < $total - ($adj * 2)))
				{
					// Affichage des numéros 1 et 2
					$pagination .= '<li><a href="'.$url.'">1</a></li>';
					$pagination .= '<li><a href="'.$url.$link.'2">2</a></li>';
					$pagination .= '<li><a href="#">&hellip;</a></li>';

					// les pages du milieu : les trois précédant la page courante, la page courante, puis les trois lui succédant
					for ($i = $current - $adj; $i <= $current + $adj; $i++)
					{
						if ($i == $current)
						{
							$pagination .= '<li class="active"><a href="#">'.$i.'</a></li>';
						} else {
							$pagination .= '<li><a href="'.$url.$link.$i.'">'.$i.'</a></li>';
						}
					}

					$pagination .= '<li><a href="#">&hellip;</a></li>';

					// et les deux derniers numéros
					$pagination .= '<li><a href="'.$url.$link.$penultimate.'">'.$penultimate.'</a></li>';
					$pagination .= '<li><a href="'.$url.$link.$total.'">'.$total.'</a></li>';
				}
				/*
					* Troncature 3 : on se situe dans la partie de droite, on tronque donc le début de la pagination.
					* l'affichage sera deux numéros de pages à gauche ... neuf à droite
					* 1 2 … 9 10 11 12 13 14 15 16 17
				*/
				else {
					// Affichage des numéros 1 et 2
					$pagination .= '<li><a href="'.$url.'">1</a></li>';
					$pagination .= '<li><a href="'.$url.$link.'2">2</a></li>';
					$pagination .= '<li><a href="#">&hellip;</a></li>';

					// puis des neuf derniers numéros
					for ($i = $total - (2 + ($adj * 2)); $i <= $total; $i++)
					{
						if ($i == $current)
						{
							$pagination .= '<li class="active"><a href="#">'.$i.'</a></li>';
						} else {
							$pagination .= '<li><a href="'.$url.$link.$i.'">'.$i.'</a></li>';
						}
					}
				}
			}

			/* ===============================
			 *  Affichage du bouton [suivant]
			 * =============================== */
			if ($current == $total)
			{
				$pagination .= '<li class="disabled"><a href="#"><i class="fa fa-angle-double-right"></i></a></li>';
			} else {
				$pagination .= '<li><a href="'.$url.$link.$next.'"><i class="fa fa-angle-double-right"></i></a></li>';
			}

			// Fermeture de la <div> d'affichage
			$pagination .= "\n";
		}
		return $pagination;
	}
	
	/*
		=================================
		REGLAGE
		=================================
	*/
	$dp_type_array = array("liste", "galerie", "table");
	if (!isset($_SESSION['option_dp_type'])) $_SESSION['option_dp_type'] = "galerie";
	if (isset($_['option_dp_type']) && in_array($_['option_dp_type'], $dp_type_array)) $_SESSION['option_dp_type'] = $_['option_dp_type'];
	$option_dp_type = $_SESSION['option_dp_type'];

	$nb_elements_array = array("6", "12", "18", "24", "30", "36");
	if (!isset($_SESSION['option_nb_elements'])) $_SESSION['option_nb_elements'] = "24";
	if (isset($_['option_nb_elements']) && in_array($_['option_nb_elements'], $nb_elements_array)) $_SESSION['option_nb_elements'] = $_['option_nb_elements'];
	$option_nb_elements = $_SESSION['option_nb_elements'];
	
	$order_array = array("`TitreVF`", "`TitreVF` DESC", "`Annee`, `ID`", "`Annee` DESC, `ID` DESC", "`ID`", "`ID` DESC");
	if (!isset($_SESSION['option_order'])) $_SESSION['option_order'] = "`TitreVF`";
	if (isset($_['option_order']) && in_array($_['option_order'], $order_array)) $_SESSION['option_order'] = $_['option_order'];
	$option_order = $_SESSION['option_order'];

	/*
		=================================
		LISTE
		=================================
	*/
	$list_search = "";
	
	$menu_table = new Menu();
	$menu_table->getMenuDBID($_['menu']);
	
	$list_total = new Table;
	$list_total = $list_total->getTotal($menu_table->getNametable());

	// Page
	if (isset($_['page']) && is_numeric($_['page']))
	{
		if ($_['page'] >= 1 && $_['page'] <= ceil($list_total[0]['nombre']/$option_nb_elements)) $page = intval($_['page']); else $page = 1;
	} else {
		$page = 1;
	}
	$offset_list = ($page-1) * $option_nb_elements;
	
	// Recherche + Filtres
	$type_array = array("acteurs", "annee", "audio", "commentaires", "duree", "edition", "filmvu", "genre", "note", "pays", "realisateurs", "reference", "soustitres", "support", "zone");
	$type_verif = array("acteurs", "audio", "genre", "pays", "realisateurs", "soustitres");
	
	if (!isset($_SESSION[$menu_table->getNametable()."_search_value"])) $_SESSION[$menu_table->getNametable()."_search_value"] = "";
	for ($i = 0; $i < count($type_array); $i++)
	{
		if (!isset($_SESSION[$menu_table->getNametable()."_search_value_".$type_array[$i]])) $_SESSION[$menu_table->getNametable()."_search_value_".$type_array[$i]] = "";
	}
	
	if (isset($_[$menu_table->getNametable()."_search_value"])) $_SESSION[$menu_table->getNametable()."_search_value"] = $_[$menu_table->getNametable()."_search_value"];
	for ($i = 0; $i < count($type_array); $i++)
	{
		if (isset($_[$menu_table->getNametable()."_search_value_".$type_array[$i]])) $_SESSION[$menu_table->getNametable()."_search_value_".$type_array[$i]] = $_[$menu_table->getNametable()."_search_value_".$type_array[$i]];
	}
	
	if (!empty($_SESSION[$menu_table->getNametable()."_search_value"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_genre"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_pays"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_annee"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_duree"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_note"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_filmvu"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_acteurs"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_realisateurs"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_commentaires"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_reference"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_support"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_edition"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_zone"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_soustitres"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_audio"]))
	{
		if (!empty($_SESSION[$menu_table->getNametable()."_search_value"]))
		{
			if (preg_match("/([a-zA-Z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ².,':() ]+) ([(][0-9]{4}+[)])/i", $_SESSION[$menu_table->getNametable()."_search_value"]))
			{
				preg_match("/([a-zA-Z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ².,':() ]+) ([(][0-9]{4}+[)])/i", $_SESSION[$menu_table->getNametable()."_search_value"], $search);
				$list_search .= " AND (`TitreVF` LIKE \"%".$search[1]."%\" OR `TitreVO` LIKE \"%".$search[1]."%\") AND `Annee` = ".$search[2];
			} else {
				$list_search .= " AND (`TitreVF` LIKE \"%".$_SESSION[$menu_table->getNametable()."_search_value"]."%\" OR `TitreVO` LIKE \"%".$_SESSION[$menu_table->getNametable()."_search_value"]."%\")";
			}
		}
		for ($i=0;$i<count($type_array);$i++)
		{
			if (in_array($type_array[$i], $type_verif)) $like = "LIKE"; else $like = "=";
			if (in_array($type_array[$i], $type_verif)) $percent = "%"; else $percent = "";
			if (!empty($_SESSION[$menu_table->getNametable()."_search_value_".$type_array[$i]])) $list_search .= " AND `".$type_array[$i]."` ".$like." \"".$percent."".$_SESSION[$menu_table->getNametable()."_search_value_".$type_array[$i]]."".$percent."\"";
		}
	}
	
	$list_search_total = new Table;
	$list_search_total = $list_search_total->getSearchtotal($menu_table->getNametable(), $list_search);
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
				<li><?php $menu_name = new Menu(); $menu_name->getMenuDBID($_['menu']); echo $menu_name->getName(); ?></li>
				<li>Liste</li>
			</ol>
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading">
						<a href="#collapse" data-toggle="collapse" class="a-box-tool">
							<h3 class="panel-title">
								Recherche + Filtres
								<div class="pull-right div-box-tool"><i class="fa fa-<?php if (!empty($_SESSION[$menu_table->getNametable()."_search_value"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_genre"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_pays"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_annee"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_duree"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_note"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_filmvu"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_acteurs"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_realisateurs"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_commentaires"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_reference"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_support"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_edition"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_zone"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_soustitres"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_audio"])) echo "minus"; else echo "plus"; ?>"></i></div>
							</h3>
						</a>
					</div>
					<div class="panel-collapse collapse <?php if (!empty($_SESSION[$menu_table->getNametable()."_search_value"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_genre"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_pays"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_annee"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_duree"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_note"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_filmvu"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_acteurs"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_realisateurs"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_commentaires"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_reference"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_support"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_edition"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_zone"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_soustitres"]) || !empty($_SESSION[$menu_table->getNametable()."_search_value_audio"])) echo "in"; ?>" id="collapse">
						<div class="panel-body">
							<div class="form-group">
								<label>Recherche par titre (VF ou VO)</label>
								<form method="post" action="./?op=list&category=<?php echo $_['category']; ?>&menu=<?php echo $_['menu']; ?>" id="searchForm">
									<div class="input-group">
										<input type="text" class="form-control" name="<?php echo $menu_table->getNametable(); ?>_search_value" value="<?php echo $_SESSION[$menu_table->getNametable().'_search_value']; ?>" id="searchField" />
										<div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></div>
										<?php if (!empty($_SESSION[$menu_table->getNametable().'_search_value'])) { ?>
											<div class="input-group-btn"><button type="submit" class="btn btn-primary" name="<?php echo $menu_table->getNametable(); ?>_search_value"><i class="fa fa-close"></i></button></div>
										<?php } ?>
									</div>
								</form>
							</div>
							<div class="row">
								<?php
									$liste_list = new Liste();
									$liste_list = $liste_list->getList($_['menu']);
								?>
								<?php foreach ($liste_list as $liste => $val_liste) { ?>
									<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
										<div class="form-group">
											<label>Filtrer par <?php echo $val_liste['name']; ?></label>
											<form method="post" action="./?op=list&category=<?php echo $_['category']; ?>&menu=<?php echo $_['menu']; ?>">
												<div class="input-group" style="width:100%;">
													<select class="form-control chosen_<?php echo $val_liste['type']; ?>" name="<?php $menu_table = new Menu(); $menu_table->getMenuDBID($_['menu']); echo $menu_table->getNametable(); ?>_search_value_<?php echo $val_liste['type']; ?>" onchange="this.form.submit()">
														<option></option>
														<?php
															$table_list = new Table;
															$table_list = $table_list->getFilterList($val_liste['type'], $menu_table->getNametable(), $list_search);
															
															$i = 0;
															$tempo_list = array();
															foreach ($table_list as $table => $val_table)
															{
																$unique_list = explode(" / ", $val_table[$val_liste['type']]);
																foreach ($unique_list as $key => $value)
																{
																	$unique_list2 = explode(" - ", $value);
																	foreach ($unique_list2 as $key => $value)
																	{
																		$unique_list2 = explode(", ", $value);
																		foreach ($unique_list2 as $key => $value)
																		{
																			$unique_list2 = explode("\r", $value);
																			foreach ($unique_list2 as $key => $value)
																			{
																				$tempo_list[$i] = $value;
																				$i++;
																			}
																		}
																	}
																}
															}
															$list = array_unique($tempo_list);
															$val_liste['sort']($list);
															foreach ($list as $key => $value)
															{
																if ($_SESSION[$menu_table->getNametable()."_search_value_".$val_liste['type']] == $value) $nfselect = "selected"; else $nfselect = "";
																echo "<option value=\"".$value."\" ".$nfselect.">".$value."</option>";
															}
														?>
													</select>
													<?php if (!empty($_SESSION[$menu_table->getNametable()."_search_value_".$val_liste['type']])) { ?>
														<div class="input-group-btn"><button type="submit" class="btn btn-primary" name="<?php echo $menu_table->getNametable(); ?>_search_value_<?php echo $val_liste['type']; ?>"><i class="fa fa-close"></i></button></div>
													<?php } ?>
												</div>
											</form>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if ($list_search_total[0]['nombre'] > 0) { ?>
				<div class="panel-group">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-4">
									<div class="form-group">
										<label>Afficher en</label>
										<form method="post">
											<select class="form-control chosen" name="option_dp_type" onchange="this.form.submit()">
												<option value="liste" <?php if ($option_dp_type == "liste") echo "selected"; ?>>Liste</option>
												<option value="galerie" <?php if ($option_dp_type == "galerie") echo "selected"; ?>>Galerie</option>
												<option value="table" <?php if ($option_dp_type == "table") echo "selected"; ?>>Table</option>
											</select>
										</form>
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-4">
									<div class="form-group">
										<label>Afficher x éléments</label>
										<form method="post" action="./?op=list&category=<?php echo $_['category']; ?>&menu=<?php echo $_['menu']; ?>">
											<select class="form-control chosen" name="option_nb_elements" onchange="this.form.submit()">
												<option value="6" <?php if ($option_nb_elements == "6") echo "selected"; ?>>6</option>
												<option value="12" <?php if ($option_nb_elements == "12") echo "selected"; ?>>12</option>
												<option value="18" <?php if ($option_nb_elements == "18") echo "selected"; ?>>18</option>
												<option value="24" <?php if ($option_nb_elements == "24") echo "selected"; ?>>24</option>
												<option value="30" <?php if ($option_nb_elements == "30") echo "selected"; ?>>30</option>
												<option value="36" <?php if ($option_nb_elements == "36") echo "selected"; ?>>36</option>
											</select>
										</form>
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-4">
									<div class="form-group">
										<label>Trier par</label>
										<form method="post">
											<select class="form-control chosen" name="option_order" onchange="this.form.submit()">
												<option value="`TitreVF`" <?php if ($option_order == "`TitreVF`") echo "selected"; ?>>Titre</option>
												<option value="`TitreVF` DESC" <?php if ($option_order == "`TitreVF` DESC") echo "selected"; ?>>Titre (desc)</option>
												<option value="`Annee`, `ID`" <?php if ($option_order == "`Annee`, `ID`") echo "selected"; ?>>Année</option>
												<option value="`Annee` DESC, `ID` DESC" <?php if ($option_order == "`Annee` DESC, `ID` DESC") echo "selected"; ?>>Année (desc)</option>
												<option value="`ID`" <?php if ($option_order == "`ID`") echo "selected"; ?>>Date d'ajout</option>
												<option value="`ID` DESC" <?php if ($option_order == "`ID` DESC") echo "selected"; ?>>Date d'ajout (desc)</option>
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
				if (!empty($_SESSION[$menu_table->getNametable().'_search_value']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_genre']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_pays']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_annee']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_duree']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_note']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_filmvu']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_acteurs']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_realisateurs']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_commentaires']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_reference']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_support']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_edition']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_zone']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_soustitres']) || !empty($_SESSION[$menu_table->getNametable().'_search_value_audio']))
				{
					if ($list_search_total[0]['nombre'] == 0)
					{
						echo '<div class="alert alert-danger"><strong>0</strong> résultat</div>';
					} elseif ($list_search_total[0]['nombre'] == 1) {
						echo '<div class="alert alert-success"><strong>'.$list_search_total[0]['nombre'].'</strong> résultat</div>';
					} else {
						echo '<div class="alert alert-success"><strong>'.$list_search_total[0]['nombre'].'</strong> résultats</div>';
					}
				}
			?>
			<?php
				$total = $list_search_total[0]['nombre'];		// nombre d'entrées dans la table
				$epp = $option_nb_elements; 					// nombre d'entrées à afficher par page
				$nbPages = ceil($total/$epp); 					// calcul du nombre de pages $nbPages (on arrondit à l'entier supérieur avec la fonction ceil())
				// Récupération du numéro de la page courante depuis l'URL avec la méthode GET
				// S'il s'agit d'un nombre on traite, sinon on garde la valeur par défaut : 1
				$current = 1;
				if (isset($_['page']) && is_numeric($_['page']))
				{
					$page = intval($_['page']);
					if ($page >= 1 && $page <= $nbPages) $current = $page;
					elseif ($page < 1) $current = 1;
					else $current = 1;
				}
				if ($nbPages > 1) echo "<nav class=\"text-center\"><ul class=\"pagination\">".paginate("./?op=list&category=".$_['category']."&menu=".$_['menu']."", "&page=", $nbPages, $current)."</ul></nav>";
			?>
			
			
			
			
			<?php if ($option_dp_type == "liste") { ?>
				liste
			<?php } ?>
			
			
			
			
			<?php if ($option_dp_type == "galerie") { ?>
				<?php
					$table_list = new Table;
					$table_list = $table_list->getListeList($menu_table->getNametable(), $list_search, $option_order, $option_nb_elements, $offset_list);
				?>
				<div class="row">
					<?php foreach ($table_list as $table => $val_table) { ?>
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
						<div class="col-xs-6 col-sm-4 col-md-2">
							<div class="thumbnail">
								<a href="./?op=detail&category=<?php echo $_['category']; ?>&menu=<?php echo $_['menu']; ?>&id=<?php echo $val_table['ID']; ?>" style="text-decoration: none; color: black;">
									<?php $filename = sprintf("./profils/".$menu_table->getNametable()."/affiches/Filmotech_%05d.jpg", $val_table['ID']); ?>
									<?php if (file_exists($filename)) echo "<div class=\"lastadd-list-detail\"><img data-original=\"".$filename."\" class=\"lazy\" alt=\"affiche\" /></div>"; else echo "<div class=\"lastadd-list-detail\"><img data-src=\"holder.js/100px100p?text=aucune \n image\" alt=\"affiche\" /></div>"; ?>
									<div class="year text-danger"><?php echo $val_table['Annee']; ?></div>
									<div class="title"><?php echo $val_table['TitreVF']; ?></div>
								</a>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			
			
			
			
			<?php if ($option_dp_type == "table") { ?>
				table
			<?php } ?>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			<?php
				$total = $list_search_total[0]['nombre'];		// nombre d'entrées dans la table
				$epp = $option_nb_elements; 					// nombre d'entrées à afficher par page
				$nbPages = ceil($total/$epp); 					// calcul du nombre de pages $nbPages (on arrondit à l'entier supérieur avec la fonction ceil())
				// Récupération du numéro de la page courante depuis l'URL avec la méthode GET
				// S'il s'agit d'un nombre on traite, sinon on garde la valeur par défaut : 1
				$current = 1;
				if (isset($_['page']) && is_numeric($_['page']))
				{
					$page = intval($_['page']);
					if ($page >= 1 && $page <= $nbPages) $current = $page;
					elseif ($page < 1) $current = 1;
					else $current = 1;
				}
				if ($nbPages > 1) echo "<nav class=\"text-center\"><ul class=\"pagination\">".paginate("./?op=list&category=".$_['category']."&menu=".$_['menu']."", "&page=", $nbPages, $current)."</ul></nav>";
			?>
		</div>
		<?php include('./template/bootstrap/includes/footer.php'); ?>
		<?php include('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>