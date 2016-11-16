<?php
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key => &$val)
	{
		Functions::secure($val);
	}

	function clean_img($texte)
	{
		$texte = mb_strtolower($texte, 'UTF-8');
		$texte = str_replace(" ", "_", $texte);
		$texte = str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å', 'î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 'ð', 'ù', 'û', 'ü', 'ú', 'ū', 'é', 'è', 'ê', 'ë', 'ç', 'ÿ', 'ñ'), array('a', 'a', 'a', 'a', 'a', 'a', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'e', 'e', 'e', 'e', 'c', 'y', 'n'), $texte);
		return $texte;
	}
	
	function date_sortie($mois)
	{
		$mois = str_replace(array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'), array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'), $mois);
		return $mois;
	}

	$category_name = new Category();
	$category_name->getCategoryDBID($_GET['category']);
	$category_name = $category_name->getName();

	$menu_name = new Menu();
	$menu_name->getMenuDBID($_GET['menu']);
	$menu_name = $menu_name->getName();

	$menu_table = new Menu();
	$menu_table->getMenuDBID($_GET['menu']);
	$menu_table = $menu_table->getNametable();

	$table_ID = new Table();
	$table_ID->getTableDBID($menu_table, $id);
	$table_ID = $table_ID->getId();

	// TitreVF
	$table_TitreVF = new Table();
	$table_TitreVF->getTableDBID($menu_table, $id);
	$table_TitreVF = $table_TitreVF->getTitrevf();

	// TitreVO
	$detail_TitreVO = new Detail();
	$detail_TitreVO->getDetailList("titre_vo", $_GET['menu']);

	$table_TitreVO = new Table();
	$table_TitreVO->getTableDBID($menu_table, $id);
	$table_TitreVO = $table_TitreVO->getTitrevo();

	// Genre
	$detail_Genre = new Detail();
	$detail_Genre->getDetailList("genre", $_GET['menu']);

	$table_Genre = new Table();
	$table_Genre->getTableDBID($menu_table, $id);
	$table_Genre = $table_Genre->getGenre();

	// Année
	$detail_Annee = new Detail();
	$detail_Annee->getDetailList("annee", $_GET['menu']);

	$table_Annee = new Table();
	$table_Annee->getTableDBID($menu_table, $id);
	$table_Annee = $table_Annee->getAnnee();

	// Durée
	$detail_Duree = new Detail();
	$detail_Duree->getDetailList("duree", $_GET['menu']);

	$table_Duree = new Table();
	$table_Duree->getTableDBID($menu_table, $id);
	$table_Duree = $table_Duree->getDuree();

	// Pays
	$detail_Pays = new Detail();
	$detail_Pays->getDetailList("pays", $_GET['menu']);

	$table_Pays = new Table();
	$table_Pays->getTableDBID($menu_table, $id);
	$table_Pays = $table_Pays->getPays();

	// Note
	$detail_Note = new Detail();
	$detail_Note->getDetailList("note", $_GET['menu']);

	$table_Note = new Table();
	$table_Note->getTableDBID($menu_table, $id);
	$table_Note = $table_Note->getNote();

	// FilmVu
	$detail_FilmVu = new Detail();
	$detail_FilmVu->getDetailList("film_vu", $_GET['menu']);

	$table_FilmVu = new Table();
	$table_FilmVu->getTableDBID($menu_table, $id);
	$table_FilmVu = $table_FilmVu->getFilmvu();

	// Bande anonce
	$detail_BA = new Detail();
	$detail_BA->getDetailList("bande_annonce", $_GET['menu']);

	$table_BAType = new Table();
	$table_BAType->getTableDBID($menu_table, $id);
	$table_BAType = $table_BAType->getBAtype();

	$table_BAChemin = new Table();
	$table_BAChemin->getTableDBID($menu_table, $id);
	$table_BAChemin = $table_BAChemin->getBAchemin();

	// Fichier
	$detail_Fichier = new Detail();
	$detail_Fichier->getDetailList("fichier", $_GET['menu']);

	$table_MediaType = new Table();
	$table_MediaType->getTableDBID($menu_table, $id);
	$table_MediaType = $table_MediaType->getMediatype();

	$table_MediaChemin = new Table();
	$table_MediaChemin->getTableDBID($menu_table, $id);
	$table_MediaChemin = $table_MediaChemin->getMediachemin();

	// Synopsis
	$detail_Synopsis = new Detail();
	$detail_Synopsis->getDetailList("synopsis", $_GET['menu']);

	$table_Synopsis = new Table();
	$table_Synopsis->getTableDBID($menu_table, $id);
	$table_Synopsis = $table_Synopsis->getSynopsis();

	// Réalisateurs
	$detail_Realisateurs = new Detail();
	$detail_Realisateurs->getDetailList("realisateurs", $_GET['menu']);

	$table_Realisateurs = new Table();
	$table_Realisateurs->getTableDBID($menu_table, $id);
	$table_Realisateurs = $table_Realisateurs->getRealisateurs();

	// Réalisateurs
	$detail_Acteurs = new Detail();
	$detail_Acteurs->getDetailList("acteurs", $_GET['menu']);

	$table_Acteurs = new Table();
	$table_Acteurs->getTableDBID($menu_table, $id);
	$table_Acteurs = $table_Acteurs->getActeurs();

	// Bonus
	$detail_Bonus = new Detail();
	$detail_Bonus->getDetailList("bonus", $_GET['menu']);

	$table_Bonus = new Table();
	$table_Bonus->getTableDBID($menu_table, $id);
	$table_Bonus = $table_Bonus->getBonus();

	// Support
	$detail_Support = new Detail();
	$detail_Support->getDetailList("support", $_GET['menu']);

	$table_Support = new Table();
	$table_Support->getTableDBID($menu_table, $id);
	$table_Support = $table_Support->getSupport();

	// Reference
	$detail_Reference = new Detail();
	$detail_Reference->getDetailList("reference", $_GET['menu']);

	$table_Reference = new Table();
	$table_Reference->getTableDBID($menu_table, $id);
	$table_Reference = $table_Reference->getReference();

	// Edition
	$detail_Edition = new Detail();
	$detail_Edition->getDetailList("edition", $_GET['menu']);

	$table_Edition = new Table();
	$table_Edition->getTableDBID($menu_table, $id);
	$table_Edition = $table_Edition->getEdition();

	// Entree Date
	$detail_EntreeDate = new Detail();
	$detail_EntreeDate->getDetailList("entree_date", $_GET['menu']);

	$table_EntreeDate = new Table();
	$table_EntreeDate->getTableDBID($menu_table, $id);
	$table_EntreeDate = $table_EntreeDate->getEntreedate();

	// Nombre Support
	$detail_NombreSupport = new Detail();
	$detail_NombreSupport->getDetailList("nombre_support", $_GET['menu']);

	$table_NombreSupport = new Table();
	$table_NombreSupport->getTableDBID($menu_table, $id);
	$table_NombreSupport = $table_NombreSupport->getNombresupport();

	// Zone
	$detail_Zone = new Detail();
	$detail_Zone->getDetailList("zone", $_GET['menu']);

	$table_Zone = new Table();
	$table_Zone->getTableDBID($menu_table, $id);
	$table_Zone = $table_Zone->getZone();

	// Audio
	$detail_Audio = new Detail();
	$detail_Audio->getDetailList("audio", $_GET['menu']);

	$table_Audio = new Table();
	$table_Audio->getTableDBID($menu_table, $id);
	$table_Audio = $table_Audio->getAudio();

	// Sous Titres
	$detail_SousTitres = new Detail();
	$detail_SousTitres->getDetailList("sous_titres", $_GET['menu']);

	$table_SousTitres = new Table();
	$table_SousTitres->getTableDBID($menu_table, $id);
	$table_SousTitres = $table_SousTitres->getSoustitres();

	/*
		=================================
		DETAIL -> FILTRE + RECHERCHE
		=================================
	*/
	$total = array("genre", "annee", "pays", "support", "zone", "acteurs", "realisateurs");
	for ($i = 0; $i < count($total); $i++)
	{
		$query = new Liste();
		$$total[$i] = $query->getNBFilter($_['menu'], $total[$i]);
	}
	
	function filter($label, $value, $category, $menu, $total)
	{
		$menu_table = new Menu();
		$menu_table->getMenuDBID($menu);
		
		$list = str_replace(" / ", " - ", $value);
		$list_filter = explode(" - ", $list);
		for ($i=0; $i<count($list_filter); $i++)
		{
			if ($total != 0)
			{
				echo '<form method="post" action="./?op=list&category='.$category.'&menu='.$menu.'" style="display:inline;">';
					$filename = "./img/".$label."s/".$list_filter[$i].".png";
					if (($i+1) == count($list_filter))
					{
						if (file_exists($filename))
						{
							echo '<button type="submit" class="nobtn" name="'.$menu_table->getNametable().'_search_value_'.$label.'" value="'.$list_filter[$i].'"><img src="'.$filename.'" style="max-width:82px;max-height:25px;" /></button>';
						} else {
							echo '<button type="submit" class="nobtn" name="'.$menu_table->getNametable().'_search_value_'.$label.'" value="'.$list_filter[$i].'"><div class="text-primary">'.$list_filter[$i].'</div></button>';
						}
					} else {
						if (file_exists($filename))
						{
							echo '<button type="submit" class="nobtn" name="'.$menu_table->getNametable().'_search_value_'.$label.'" value="'.$list_filter[$i].'"><img src="'.$filename.'" style="max-width:82px;max-height:25px;" /></button> / ';
						} else {
							echo '<button type="submit" class="nobtn" name="'.$menu_table->getNametable().'_search_value_'.$label.'" value="'.$list_filter[$i].'"><div class="text-primary">'.$list_filter[$i].'</div></button> / ';
						}
					}
				echo "</form>";
			} else {
				$filename = "./img/".$label."s/".$list_filter[$i].".png";
				if (($i+1) == count($list_filter))
				{
					if (file_exists($filename))
					{
						echo '<img src="'.$filename.'" style="max-width:82px;max-height:25px;" />';
					} else {
						echo $list_filter[$i];
					}
				} else {
					if (file_exists($filename))
					{
						echo '<img src="'.$filename.'" style="max-width:82px;max-height:25px;" /> / ';
					} else {
						echo $list_filter[$i]." / ";
					}
				}
			}
		}
	}

	function search($label, $value, $category, $menu, $total)
	{
		$menu_table = new Menu();
		$menu_table->getMenuDBID($menu);
		
		$list = str_replace("\r", "|", $value);
		$list_search = explode("|", $list);
		for ($i=0;$i<count($list_search);$i++)
		{
			if ($total != 0)
			{
				echo "<div class=\"thumbnail\">";
					echo '<form method="post" action="./?op=list&category='.$category.'&menu='.$menu.'" style="display:inline;">';
						echo '<button type="submit" class="nobtn-actor" name="'.$menu_table->getNametable().'_search_value_'.$label.'" value="'.$list_search[$i].'">';
							$filename = "./img/real_acteur/".clean_img($list_search[$i]).".jpg";
							if (file_exists($filename))
							{
								echo '<img src="'.$filename.'" alt="'.$list_search[$i].'" />';
								echo "<div class=\"title\">".$list_search[$i]."</div>";
							} else {
								echo '<img src="./img/nobody.jpg" alt="'.$list_search[$i].'" />';
								echo "<div class=\"title\">".$list_search[$i]."</div>";
							}
						echo "</button>";
					echo "</form>";
				echo "</div>";
			} else {
				echo "<div class=\"thumbnail\">";
					$filename = "./img/real_acteur/".clean_img($list_search[$i]).".jpg";
					if (file_exists($filename))
					{
						echo '<img src="'.$filename.'" alt="'.$list_search[$i].'" />';
						echo "<div class=\"title\">".$list_search[$i]."</div>";
					} else {
						echo '<img src="./img/nobody.jpg" alt="'.$list_search[$i].'" />';
						echo "<div class=\"title\">".$list_search[$i]."</div>";
					}
				echo "</div>";
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<?php include('./template/bootstrap/includes/header.php'); ?>
	<body>
		<?php include('./template/bootstrap/includes/navbar.php'); ?>
		<div class="container">
			<ol class="breadcrumb">
				<li><i class="fa fa-home"></i></li>
				<li><?php echo $category_name; ?></li>
				<li><?php echo $menu_name; ?></li>
				<li><?php echo $table_TitreVF; ?></li>
			</ol>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-8 text-center">
					<!-- AFFICHE -->
					<?php $filename = sprintf('./profils/'.$menu_table.'/affiches/Filmotech_%05d.jpg', $table_ID); ?>
					<?php echo "<div class=\"detail\" id=\"affiche\"><a href=\"".$filename."\"><img src=\"".$filename."\" class=\"detail-img\" alt=\"".$table_TitreVF."\" /></a></div>"; ?>
					<br />
				</div>
				<div class="col-xs-12 col-sm-12 col-md-4">
					<!-- DETAILS -->
					<div class="panel">
						<li class="list-group-item"><i class="fa fa-pencil"></i> <?php echo $table_TitreVF; ?></li>
						<?php if (!empty($detail_TitreVO->getType())) { ?><li class="list-group-item"><i class="fa fa-<?php echo $detail_TitreVO->getIcon(); ?>"></i> <?php echo $table_TitreVO; ?></li> <?php } ?>
						<?php if (!empty($detail_Genre->getType())) { ?> <li class="list-group-item"><i class="fa fa-<?php echo $detail_Genre->getIcon(); ?>"></i> <?php echo filter("genre", $table_Genre, $_['category'], $_['menu'], $genre[0]['nombre']); ?></li> <?php } ?>
						<?php if (!empty($detail_Annee->getType()) || !empty($detail_Duree->getType()) || !empty($detail_Pays->getType())) { ?>
							<li class="list-group-item">
								<?php if (!empty($detail_Annee->getType())) { ?><i class="fa fa-<?php echo $detail_Annee->getIcon(); ?>"></i> <?php echo filter("annee", $table_Annee, $_['category'], $_['menu'], $annee[0]['nombre']); ?><br/><?php } ?>
								<?php if (!empty($detail_Duree->getType())) { ?>
									<?php
										if ($detail_Duree->getOptions() == "pages")
										{
											$duree = $table_Duree." pages";
										}
										if ($detail_Duree->getOptions() == "temps")
										{
											if ($table_Duree < '60')
											{
												$duree = ($table_Duree%60).'min';
											}
											elseif ($table_Duree > '60')
											{
												$duree = floor($table_Duree/60).'h '.($table_Duree%60).'min';
											}
										}
									?>
									<i class="fa fa-<?php echo $detail_Duree->getIcon(); ?>"></i> <?php echo $duree; ?><br/>
								<?php } ?>
								<?php if (!empty($detail_Pays->getType())) { ?><i class="fa fa-<?php echo $detail_Pays->getIcon(); ?>"></i> <?php echo filter("pays", $table_Pays, $_['category'], $_['menu'], $pays[0]['nombre']); ?><?php } ?>
							</li>
						<?php } ?>
						<?php
							if (!empty($detail_Note->getType()))
							{
								if (file_exists("./img/stars/".$table_Note.".png")) $note = "<img src=\"./img/stars/".$table_Note.".png\" alt=\"note\" />"; else $note = $table_Note;
								echo "<li class=\"list-group-item\"><i class=\"fa fa-".$detail_Note->getIcon()."\"></i> ".$note."</li>";
							}
						?>
						<?php
							if (!empty($detail_FilmVu->getType()) || !empty($detail_BA->getType()) || !empty($detail_Fichier->getType()))
							{
								echo "<li class=\"list-group-item\">";
									echo "<div class=\"row\">";
										echo "<div class=\"col-xs-12 col-sm-6 col-md-6\">";
											if (!empty($detail_FilmVu->getType()))
											{
												if ($table_FilmVu == "NON") echo "<div class=\"btn btn-danger btn-block\" disabled=\"disabled\"><i class=\"fa fa-eye-slash\"></i> Film non vu</div>";
												if ($table_FilmVu == "OUI") echo "<div class=\"btn btn-primary btn-block\" disabled=\"disabled\"><i class=\"fa fa-eye\"></i> Film vu</div>";
											}
										echo "</div>";
										if ($table_BAType == "URL" && !empty($table_BAChemin))
										{
											echo "<div class=\"col-xs-12 col-sm-6 col-md-6\">";
												if (!empty($detail_BA->getType()))
												{
													echo "<div id=\"bandeannonce\"><a href=\"".$table_BAChemin."\" class=\"btn btn-default btn-block\"><i class=\"fa fa-".$detail_BA->getIcon()."\"></i> ".$detail_BA->getName()."</a></div>";
												}
											echo "</div>";
										}
										if ($table_MediaType == "Fichier" && !empty($table_MediaChemin))
										{
											echo "<div class=\"col-xs-12 col-sm-12 col-md-12\">";
												if (!empty($detail_Fichier->getType()))
												{
													echo "<br/>";
													echo "<a href=\"".$table_MediaChemin."\" class=\"btn btn-default btn-block\"><i class=\"fa fa-".$detail_Fichier->getIcon()."\"></i> ".$detail_Fichier->getName()."</a>";
												}
											echo "</div>";
										}
									echo "</div>";
								echo "</li>";
							}
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-8">
					<!-- SYNOPSIS -->
					<?php if (!empty($detail_Synopsis->getType())) { ?>
						<div class="panel panel-default">
							<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-<?php echo $detail_Synopsis->getIcon(); ?>"></i> <?php echo $detail_Synopsis->getName(); ?></h3></div>
							<div class="panel-body"><?php echo str_replace("\r", "<br/>", $table_Synopsis); ?></div>
						</div>
					<?php } ?>
					<!-- REALISATEUR -->
					<?php if (!empty($detail_Realisateurs->getType())) { ?>
						<div class="panel panel-default">
							<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-<?php echo $detail_Realisateurs->getIcon(); ?>"></i> <?php echo $detail_Realisateurs->getName(); ?></h3></div>
							<div class="panel-body"><div class="regular"><?php echo search("realisateurs", $table_Realisateurs, $_['category'], $_['menu'], $realisateurs[0]['nombre']); ?></div></div>
						</div>
					<?php } ?>
					<!-- ACTEURS -->
					<?php if (!empty($detail_Acteurs->getType())) { ?>
						<div class="panel panel-default">
							<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-<?php echo $detail_Acteurs->getIcon(); ?>"></i> <?php echo $detail_Acteurs->getName(); ?></h3></div>
							<div class="panel-body"><div class="regular"><?php echo search("acteurs", $table_Acteurs, $_['category'], $_['menu'], $acteurs[0]['nombre']); ?></div></div>
						</div>
					<?php } ?>
					<!-- INFORMATIONS SUPPLEMENTAIRES -->
					<?php if (!empty($detail_Bonus->getType())) { ?>
						<div class="panel panel-default">
							<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-<?php echo $detail_Bonus->getIcon(); ?>"></i> <?php echo $detail_Bonus->getName(); ?></h3></div>
							<div class="panel-body"><?php echo str_replace("\r", "<br/>", $table_Bonus); ?></div>
						</div>
					<?php } ?>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-4">
					<!-- DETAILS -->
					<?php if (!empty($detail_Support->getType()) || !empty($detail_Edition->getType()) || !empty($detail_Reference->getType()) || !empty($detail_EntreeDate->getType()) || !empty($detail_NombreSupport->getType()) || !empty($detail_Zone->getType())) { ?>
						<div class="panel panel-default">
							<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-paperclip"></i> Détails</h3></div>
							<div class="panel-body">
								<?php if (!empty($detail_Support->getType())) { ?><i class="fa fa-<?php echo $detail_Support->getIcon(); ?>"></i> <strong><?php echo $detail_Support->getName(); ?> : </strong><?php echo filter("support", $table_Support, $_['category'], $_['menu'], $support[0]['nombre']); ?><br/><?php } ?>
								<?php if (!empty($detail_Edition->getType()) && !empty($table_Edition)) echo "<i class=\"fa fa-".$detail_Edition->getIcon()."\"></i> <strong>".$detail_Edition->getName()." : </strong>".$table_Edition."<br/>"; ?>
								<?php if (!empty($detail_Reference->getType())) echo "<i class=\"fa fa-".$detail_Reference->getIcon()."\"></i> <strong>".$detail_Reference->getName()." : </strong>".$table_Reference."<br/>"; ?>
								<?php if (!empty($detail_EntreeDate->getType())) echo "<i class=\"fa fa-".$detail_EntreeDate->getIcon()."\"></i> <strong>".$detail_EntreeDate->getName()." : </strong>".date_sortie(date('d F Y', strtotime($table_EntreeDate)))."<br/>"; ?>
								<?php if (!empty($detail_NombreSupport->getType())) echo "<i class=\"fa fa-".$detail_NombreSupport->getIcon()."\"></i> <strong>".$detail_NombreSupport->getName()." : </strong>".$table_NombreSupport."<br/>"; ?>
								<?php if (!empty($detail_Zone->getType())) { ?><i class="fa fa-<?php echo $detail_Zone->getIcon(); ?>"></i> <strong><?php echo $detail_Zone->getName(); ?> : </strong><?php echo filter("zone", $table_Zone, $_['category'], $_['menu'], $zone[0]['nombre']); ?><?php } ?>
							</div>
						</div>
					<?php } ?>
					<!-- AUDIO -->
					<?php if (!empty($detail_Audio->getType())) { ?>
						<div class="panel panel-default">
							<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-<?php echo $detail_Audio->getIcon(); ?>"></i> <?php echo $detail_Audio->getName(); ?></h3></div>
							<div class="panel-body">
								<table width="100%">
									<?php
										$liste_audio = explode(", ", $table_Audio);
										for ($i=0;$i<count($liste_audio);$i++)
										{
											preg_match("/^([a-zA-Z-]+)(\d.+) ([a-zA-ZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ]+)/i", $liste_audio[$i], $audio);
											echo "<tr height=\"30px\">";
											if (file_exists("./img/flags/".clean_img($audio[3]).".png"))
											{
												echo "<td><img src=\"./img/flags/".clean_img($audio[3]).".png\" style=\"max-width:20px;\" title=\"".$audio[3]."\" /> ".$audio[3]."</td>";
											} else {
												echo "<td>".$audio[3]."</td>";
											}
											if (file_exists("./img/audiocodec/".$audio[1].".png"))
											{
												echo "<td style=\"width:30%\" class=\"text-center\"><img src=\"./img/audiocodec/".$audio[1].".png\" style=\"max-width:82px;max-height:25px;\" title=\"".$audio[1]."\" /></td>";
											} else {
												echo "<td style=\"width:30%\" class=\"text-center\">".$audio[1]."</td>";
											}
											if (file_exists("./img/audiochannel/".$audio[2].".png"))
											{
												echo "<td style=\"width:30%\" class=\"text-right\"><img src=\"./img/audiochannel/".$audio[2].".png\" style=\"max-width:82px;max-height:25px;\" title=\"".$audio[2]."\" /></td>";
											} else {
												echo "<td style=\"width:30%\" class=\"text-right\">".$audio[2]."</td>";
											}
											echo "</tr>";
										}
									?>
								</table>
							</div>
						</div>
					<?php } ?>
					<!-- SOUS-TITRES -->
					<?php if (!empty($detail_SousTitres->getType())) { ?>
						<div class="panel panel-default">
							<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-<?php echo $detail_SousTitres->getIcon(); ?>"></i> <?php echo $detail_SousTitres->getName(); ?></h3></div>
							<div class="panel-body">
								<?php
									$soustitres = explode(", ", $table_SousTitres);
									for ($i=0;$i<count($soustitres);$i++)
									{
										if (file_exists("./img/flags/".clean_img($soustitres[$i]).".png"))
										{
											echo "<img src=\"./img/flags/".clean_img($soustitres[$i]).".png\" style=\"width:20px\" title=\"".$soustitres[$i]."\" /> ".$soustitres[$i]."<br/>";
										} else {
											echo $soustitres[$i]."<br/>";
										}
									}
								?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php include('./template/bootstrap/includes/footer.php'); ?>
		<?php include('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>