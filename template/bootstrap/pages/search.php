<?php
	if (!isset($_SESSION['search']))
	{
		$_SESSION['search'] = "";
	}


	if (isset($_POST['searchButton']) && $_POST['searchButton'] == 1)
	{
		$_SESSION['search'] = $_POST['essai'];
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
				<li>Recherche</li>
			</ol>
		
			<form method="post" action="./?op=search">
				<input type="hidden" name="searchButton" value="1" />
					<div class="form-group">
						<input type="text" class="form-control" name="essai" value="<?php echo $_SESSION['search']; ?>" placeholder="Tapez votre recherche ici" />
					</div>
			</form>
			
			
			
			
			
			<?php
				$category = new Category();
				$liste_category = $category->getCategoryList();
			?>
			<?php foreach ($liste_category as $category => $val_category) { ?>
				<?php if ($_SESSION['search'] != "") { ?>
					<?php
						$menu_list = new Menu();
						$menu_list = $menu_list->getMenuDBIDCategory($val_category['id']);
					?>
					<?php foreach ($menu_list as $menu => $val_menu) { ?>
						<?php
							$search_list = new Search();
							$search_list = $search_list->getSearchList($val_menu['name_table'], $_SESSION['search']);
						?>
						<?php if ($search_list) { ?>
							<div class="panel panel-default">
								<div class="panel-heading">Les derniers ajouts de la table <a href="./?op=list&category=<?php echo $val_menu['id_category']; ?>&menu=<?php echo $val_menu['id']; ?>"><?php echo $val_menu['name']; ?></a></div>
								<div class="panel-body">
									<div class="regular slider">
										<?php foreach ($search_list as $search => $val_search) { ?>
											<?php
												if ($val_search['Duree'] < "60")
												{
													$duree = ($val_search['Duree']%60)."min";
												}
												elseif ($val_search['Duree'] > "60")
												{
													$duree = floor($val_search['Duree']/60)."h ".($val_search['Duree']%60)."min";
												}
											?>
											<div class="img">
												<a href="./?op=detail&category=<?php echo $val_menu['id_category']; ?>&menu=<?php echo $val_menu['id']; ?>&id=<?php echo $val_search['ID']; ?>" style="text-decoration: none; color: black;" data-container="body" data-toggle="popover" data-style="primary" data-title="<strong><?php echo $val_search['TitreVF']; ?></strong><br/><?php echo $val_search['Annee']; ?> | <?php echo $duree; ?> | <?php echo $val_search['Genre']; ?>" data-content="<div class='popover-synopsis'><?php echo htmlspecialchars($val_search['Synopsis'], ENT_QUOTES); ?></div><br/><div class='popover-real-actor'>Un film de : <?php echo str_replace("\r", " / ", $val_search['Realisateurs']); ?><br/>Avec : <?php echo str_replace("\r", " / ", $val_search['Acteurs']); ?></div>">
													<?php $filename = sprintf("./profils/".$val_menu['name_table']."/affiches/Filmotech_%05d.jpg", $val_search['ID']); ?>
													<img src="./img/bg_blank.png" style="background: url('<?php echo $filename; ?>') no-repeat; background-size: 100% auto;" alt="Affiche" />
													<div class="title"><?php echo $val_search['TitreVF']; ?></div>
												</a>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			<?php } ?>
				
				
				
				
				
				
				
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		</div>
		<?php include('./template/bootstrap/includes/footer.php'); ?>
		<?php include('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>