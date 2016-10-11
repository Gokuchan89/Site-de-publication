<?php
	if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1)
	{
		header("location: ./");
		exit();
	}

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key => &$val)
	{
		Functions::secure($val);
	}

	$lib_errors = "Erreurs";

	/*
		=================================
		PARAMETRES GENERAUX -> MODIFICATION
		=================================
	*/
	if (isset($_['settingsButton']) && $_['settingsButton'] == 1 && empty($test[$lib_errors]))
	{
		$setting = new Setting();
		
		// Titre du site
		$setting->getSettingDBKey('title');
		$setting->setValue($_['settings_title']);
		$setting->saveSetting();
		
		// Message sur la page d'accueil
		$setting->getSettingDBKey('message_home');
		$setting->setValue($_['settings_message_home']);
		$setting->saveSetting();
		
		// Ouvert au public
		$setting->getSettingDBKey('open');
		$setting->setValue($_['settings_open']);
		$setting->saveSetting();
		
		// Inscription autorisée
		$setting->getSettingDBKey('registration');
		$setting->setValue($_['settings_registration']);
		$setting->saveSetting();
		
		// Permettre l'accès 'Invité'
		$setting->getSettingDBKey('invite');
		$setting->setValue($_['settings_invite']);
		$setting->saveSetting();		// Activer le mode de maintenance
		$setting->getSettingDBKey('maintenance');
		$setting->setValue($_['settings_maintenance']);
		$setting->saveSetting();
		
		// Largeur maximum de l'avatar
		$setting->getSettingDBKey('avatar_width');
		$setting->setValue($_['settings_avatar_width']);
		$setting->saveSetting();
		
		// Message de maintenance
		$setting->getSettingDBKey('message_maintenance');
		$setting->setValue($_['settings_message_maintenance']);
		$setting->saveSetting();
		
		// Hauteur maximum de l'avatar
		$setting->getSettingDBKey('avatar_height');
		$setting->setValue($_['settings_avatar_height']);
		$setting->saveSetting();
		
		// Poids maximum de l'avatar
		$setting->getSettingDBKey('avatar_weight');
		$setting->setValue($_['settings_avatar_weight']);
		$setting->saveSetting();
		
		// Nombre d'éléments 'Derniers ajouts'
		$setting->getSettingDBKey('lastadd_max');
		$setting->setValue($_['settings_lastadd_max']);
		$setting->saveSetting();
	
		$log = new Log_activite();
		$log->setUsername($_SESSION['name']);
		$log->setModule("Administration");
		$log->setAction("Paramètres");
		$log->setComment("Les paramètres généraux ont été modifiés");
		$log->saveLog_activite();
	}

	/*
		=================================
		CATEGORIES -> AJOUT
		=================================
	*/
	if (isset($_['categoryAddButton']) && $_['categoryAddButton'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_['category_add_name']))
		{
			$category = new Category();
			$category->setName($_['category_add_name']);
			$category->saveCategory();
		} else {
			$test[$lib_errors][] = "Il est nécessaire de fournir un nom pour la catégorie.";
		}
	}

	/*
		=================================
		CATEGORIES -> MODIFICATION
		=================================
	*/
	if (isset($_['categoryEditButton']) && $_['categoryEditButton'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_['category_edit_name']))
		{
			$category = new Category();
			$category->getCategoryDBID($id);
			$category->setName($_['category_edit_name']);
			$category->saveCategory();
		} else {
			$test[$lib_errors][] = "Il est nécessaire de fournir un nom pour la catégorie.";
		}
	}

	/*
		=================================
		MENUS -> AJOUT
		=================================
	*/
	if (isset($_['menuAddButton']) && $_['menuAddButton'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_['menu_add_category']) && !empty($_['menu_add_name']) && !empty($_['menu_add_icon']) && !empty($_['menu_add_table']))
		{
			$menu = new Menu();
			$menu->setName($_['menu_add_name']);
			$menu->setIcon($_['menu_add_icon']);
			$menu->setPosition(1);
			$menu->setNametable($_['menu_add_table']);
			$menu->setIDcategory($_['menu_add_category']);
			$menu->saveMenu();

			if (!file_exists("./profils/".$_['menu_add_table']))
			{
				$old = umask(0);
				mkdir("./profils/".$_['menu_add_table'], 0777);
				umask($old);
			}
		} else {
			$test[$lib_errors][] = "Il est nécessaire de fournir le nom et l'icone du menu, ainsi que le nom de la table.";
		}
	}

	/*
		=================================
		MENUS -> MODIFICATION
		=================================
	*/
	if (isset($_['menuEditButton']) && $_['menuEditButton'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_['menu_edit_position']) && !empty($_['menu_edit_name']) && !empty($_['menu_edit_table']) && !empty($_['menu_edit_icon']) && !empty($_['menu_edit_category']))
		{
			$menu = new Menu();
			$menu->getMenuDBID($id);
			$menu->setName($_['menu_edit_name']);
			$menu->setIcon($_['menu_edit_icon']);
			$menu->setPosition($_POST['menu_edit_position']);
			$menu->setNametable($_['menu_edit_table']);
			$menu->setIDcategory($_['menu_edit_category']);
			$menu->saveMenu();
		} else {
			$test[$lib_errors][] = "Il est nécessaire de fournir la position, le nom et l'icone du menu, le nom de la table, ainsi que la catégorie.";
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
				<li>Administration</li>
				<li>Paramètres</li>
			</ol>
			<div class="btn-group btn-group-justified">
				<div class="btn-group"><a href="./?op=settings" class="btn btn-default <?php if ($tab == 1) echo 'active'; ?>">Paramètres généraux</a></div>
				<div class="btn-group"><a href="./?op=settings&tab=2" class="btn btn-default <?php if ($tab == 2) echo 'active'; ?>">Catégories + Menus</a></div>
				<div class="btn-group"><a href="./?op=settings&tab=3" class="btn btn-default <?php if ($tab == 3) echo 'active'; ?>">Listes + Détails</a></div>
			</div>
			<br/>
			<?php if ($tab == 1) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">Paramètres généraux</div>
					<form method="post" class="form-horizontal" action="./?op=settings" id="settingsForm">
						<input type="hidden" name="settingsButton" value="1">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">Titre du site</label>
								<div class="col-sm-8"><input type="text" class="form-control" name="settings_title" value="<?php $setting_title = new Setting(); $setting_title->getSettingDBKey('title'); echo $setting_title->getValue(); ?>" required /></div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Message sur la page d'accueil</label>
								<div class="col-sm-8"><textarea class="textarea" name="settings_message_home" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php $setting_message_home = new Setting(); $setting_message_home->getSettingDBKey('message_home'); echo $setting_message_home->getValue(); ?></textarea></div>
							</div>
							<hr>
							<div class="form-group">
								<label class="col-sm-4 control-label">Ouvert au public ?</label>
								<div class="col-sm-8" style="margin-top:5px;">
									<input type="radio" name="settings_open" value="0" <?php $setting_open = new Setting(); $setting_open->getSettingDBKey('open'); if ($setting_open->getValue() == 0) echo "checked"; ?>> Non
									<input type="radio" name="settings_open" value="1" <?php $setting_open = new Setting(); $setting_open->getSettingDBKey('open'); if ($setting_open->getValue() == 1) echo "checked"; ?>> Oui
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Inscription autorisée ?</label>
								<div class="col-sm-8" style="margin-top:5px;">
									<input type="radio" name="settings_registration" value="0" <?php $setting_registration = new Setting(); $setting_registration->getSettingDBKey('registration'); if ($setting_registration->getValue() == 0) echo 'checked'; ?>> Non
									<input type="radio" name="settings_registration" value="1" <?php $setting_registration = new Setting(); $setting_registration->getSettingDBKey('registration'); if ($setting_registration->getValue() == 1) echo 'checked'; ?>> Oui
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Permettre l'accès "Invité" ?</label>
								<div class="col-sm-8" style="margin-top:5px;">
									<input type="radio" name="settings_invite" value="0" <?php $setting_invite = new Setting(); $setting_invite->getSettingDBKey('invite'); if ($setting_invite->getValue() == 0) echo 'checked'; ?>> Non
									<input type="radio" name="settings_invite" value="1" <?php $setting_invite = new Setting(); $setting_invite->getSettingDBKey('invite'); if ($setting_invite->getValue() == 1) echo 'checked'; ?>> Oui
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Activer le mode de maintenance ?</label>
								<div class="col-sm-8" style="margin-top:5px;">
									<input type="radio" name="settings_maintenance" value="0" <?php $setting_maintenance = new Setting(); $setting_maintenance->getSettingDBKey('maintenance'); if ($setting_maintenance->getValue() == 0) echo 'checked'; ?>> Non
									<input type="radio" name="settings_maintenance" value="1" <?php $setting_maintenance = new Setting(); $setting_maintenance->getSettingDBKey('maintenance'); if ($setting_maintenance->getValue() == 1) echo 'checked'; ?>> Oui
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Message de maintenance</label>
								<div class="col-sm-8">
									<textarea class="textarea" name="settings_message_maintenance" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php $setting_message_maintenance = new Setting(); $setting_message_maintenance->getSettingDBKey('message_maintenance'); echo $setting_message_maintenance->getValue(); ?></textarea>
								</div>
							</div>
							<hr>
							<div class="form-group">
								<label class="col-sm-4 control-label">Largeur maximum de l'avatar</label>
								<div class="col-sm-8"><input type="number" class="form-control" name="settings_avatar_width" value="<?php $setting_avatar_width = new Setting(); $setting_avatar_width->getSettingDBKey('avatar_width'); echo $setting_avatar_width->getValue(); ?>" required /></div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Hauteur maximum de l'avatar</label>
								<div class="col-sm-8"><input type="number" class="form-control" name="settings_avatar_height" value="<?php $setting_avatar_height = new Setting(); $setting_avatar_height->getSettingDBKey('avatar_height'); echo $setting_avatar_height->getValue(); ?>" required /></div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Poids maximum de l'avatar</label>
								<div class="col-sm-8"><input type="number" class="form-control" name="settings_avatar_weight" value="<?php $setting_avatar_weight = new Setting(); $setting_avatar_weight->getSettingDBKey('avatar_weight'); echo $setting_avatar_weight->getValue(); ?>" required /></div>
							</div>
							<hr>
							<div class="form-group">
								<label class="col-sm-4 control-label">Nombre d'éléments "Derniers ajouts"</label>
								<div class="col-sm-8"><input type="number" class="form-control" name="settings_lastadd_max" value="<?php $setting_lastadd_max = new Setting(); $setting_lastadd_max->getSettingDBKey('lastadd_max'); echo $setting_lastadd_max->getValue(); ?>" required /></div>
							</div>
						</div>
						<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Modifier</button></div>
					</form>
				</div>
			<?php } ?>
			<?php if ($tab == 2) { ?>
				<!-- SUPPRESSION D'UNE CATEGORIE -->
				<div class="modal fade" id="ConfirmSupprCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header"><h4 class="modal-title" id="myModalLabel">Supprimer une catégorie</h4></div>
							<div class="modal-body">
								<p>Voulez-vous vraiment supprimer cette catégorie ?</p>
								<p>Cela supprimera tous les menus qu'elle contient.</p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
								<button type="button" class="btn btn-primary" onclick="delCategory()" data-dismiss="modal">Oui</button>
							</div>
						</div>
					</div>
				</div>
				<!-- SUPPRESSION D'UN MENU -->
				<div class="modal fade" id="ConfirmSupprMenu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header"><h4 class="modal-title" id="myModalLabel">Supprimer un menu</h4></div>
							<div class="modal-body">
								<p>Voulez-vous vraiment supprimer ce menu ?</p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
								<button type="button" class="btn btn-primary" onclick="delMenu()" data-dismiss="modal">Oui</button>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-4">
						<div class="panel panel-default">
							<div class="panel-heading">Catégories + Menus</div>
							<table class="table table-bordered">
								<?php
									$category = new Category();
									$liste_category = $category->getCategoryList();
								?>
								<?php foreach ($liste_category as $category => $val_category) { ?>
									<tr>
										<td style="width:96%;background-color:#f9f9f9;" colspan="2"><?php echo $val_category['name']; ?></td>
										<td class="text-center" style="background-color:#f9f9f9;"><a href="./?op=settings&tab=2&type=category_edit&id=<?php echo $val_category['id']; ?>" class="btn btn-info btn-xs" title="Modifier la catégorie"><i class="fa fa-pencil"></i></a></td>
										<td class="text-center" style="background-color:#f9f9f9;"><button class="btn btn-danger btn-xs" onclick="category_del(<?php echo $val_category['id']; ?>)" title="Supprimer la catégorie"><i class="fa fa-trash-o"></i></button></td>
									</tr>
									<?php
										$menu = new Menu();
										$liste_menu = $menu->getMenuDBIDCategory($val_category['id']);
									?>
									<?php foreach ($liste_menu as $menu => $val_menu) { ?>
										<tr>
											<td><i class="fa fa-<?php echo $val_menu['icon']; ?>"></i></td>
											<td style="width:89%;"><?php echo $val_menu['name']; ?></td>
											<td class="text-center"><a href="./?op=settings&tab=2&type=menu_edit&id=<?php echo $val_menu['id']; ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a></td>
											<td class="text-center"><button class="btn btn-danger btn-xs" onclick="menu_del(<?php echo $val_menu['id']; ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button></td>
										</tr>
									<?php } ?>
								<?php } ?>
								<tr><td colspan="5"><a href="./?op=settings&tab=2&type=category_add"><i class="fa fa-plus"></i> Ajouter une catégorie</a></td></tr>
								<tr><td colspan="5"><a href="./?op=settings&tab=2&type=menu_add"><i class="fa fa-plus"></i> Ajouter un menu</a></td></tr>
							</table>
						</div>
					</div>
					<?php if ($type == 'category_add') { ?>
						<div class="col-xs-12 col-sm-12 col-md-8">
							<div class="panel panel-default">
								<div class="panel-heading">Ajouter une catégorie</div>
								<form method="post" action="./?op=settings&tab=2&type=category_add" id="categoryAddForm">
									<input type="hidden" name="categoryAddButton" value="1">
									<div class="panel-body">
										<?php
											if (!empty($test[$lib_errors]))
											{
												foreach ($test as $type=>$messages)
												{
													foreach ($messages as $message)
													{
														echo "<div class=\"alert alert-danger\">".$message."</div>";
													}
												}
											}
										?>
										<div class="form-group has-feedback">
											<input type="text" class="form-control" name="category_add_name" placeholder="Nom de la catégorie" required />
											<span class="form-control-feedback"><i class="fa fa-archive"></i></span>
										</div>
									</div>
									<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Ajouter</button></div>
								</form>
							</div>
						</div>
					<?php } ?>
					<?php if ($type == 'category_edit') { ?>
						<div class="col-xs-12 col-sm-12 col-md-8">
							<div class="panel panel-default">
								<div class="panel-heading">Modifier une catégorie</div>
								<form method="post" action="./?op=settings&tab=2&type=category_edit&id=<?php echo $id; ?>" id="categoryEditForm">
									<input type="hidden" name="categoryEditButton" value="1">
									<div class="panel-body">
										<?php
											if (!empty($test[$lib_errors]))
											{
												foreach ($test as $type=>$messages)
												{
													foreach ($messages as $message)
													{
														echo "<div class=\"alert alert-danger\">".$message."</div>";
													}
												}
											}
										?>
										<div class="form-group">
											<label>Nom de la catégorie</label>
											<input type="text" class="form-control" name="category_edit_name" value="<?php $catgory_name = new Category(); $catgory_name->getCategoryDBID($id); echo $catgory_name->getName(); ?>" required />
										</div>
									</div>
									<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Modifier</button></div>
								</form>
							</div>
						</div>
					<?php } ?>
					<?php if ($type == 'menu_add') { ?>
						<div class="col-xs-12 col-sm-12 col-md-8">
							<div class="panel panel-default">
								<div class="panel-heading">Ajouter un menu</div>
								<form method="post" action="./?op=settings&tab=2&type=menu_add" id="menuAddForm">
									<input type="hidden" name="menuAddButton" value="1">
									<div class="panel-body">
										<?php
											if (!empty($test[$lib_errors]))
											{
												foreach ($test as $type=>$messages)
												{
													foreach ($messages as $message)
													{
														echo "<div class=\"alert alert-danger\">".$message."</div>";
													}
												}
											}
										?>
										<h4>Catégorie</h4>
										<div class="form-group">
											<select class="form-control chosen" name="menu_add_category" required>
												<option></option>
												<?php
													$category = new Category();
													$liste_category = $category->getCategoryList();
													foreach ($liste_category as $category => $val_category)
													{
														echo "<option value=\"".$val_category['id']."\">".$val_category['name']."</option>";
													}
												?>
											</select>
										</div>
										<hr>
										<h4>Menu</h4>
										<div class="form-group has-feedback">
											<input type="text" class="form-control" name="menu_add_name" placeholder="Nom du menu" required />
											<span class="form-control-feedback"><i class="fa fa-pencil"></i></span>
										</div>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control" name="menu_add_icon" id="menuIcon" placeholder="Nom de l'icône" required />
												<div class="input-group-btn">
													<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><i class="fa fa-info"></i></button>
													<ul class="dropdown-menu dropdown-menu-right">
														<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='align-justify'"><i class="fa fa-align-justify"></i> align-justify</a></li>
														<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='book'"><i class="fa fa-book"></i> book</a></li>
														<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='film'"><i class="fa fa-film"></i> film</a></li>
														<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='gamepad'"><i class="fa fa-gamepad"></i> gamepad</a></li>
														<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='music'"><i class="fa fa-music"></i> music</a></li>
													</ul>
												</div>
											</div>
										</div>
										<div class="form-group has-feedback">
											<input type="text" class="form-control" name="menu_add_table" placeholder="Nom de la table" required />
											<span class="form-control-feedback"><i class="fa fa-table"></i></span>
										</div>
									</div>
									<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Ajouter</button></div>
								</form>
							</div>
						</div>
					<?php } ?>
					<?php if ($type == 'menu_edit') { ?>
						<div class="col-xs-12 col-sm-12 col-md-8">
							<div class="panel panel-default">
								<div class="panel-heading">Modifier un menu</div>
								<form method="post" action="./?op=settings&tab=2&type=menu_edit&id=<?php echo $id; ?>" id="menuEditForm">
									<input type="hidden" name="menuEditButton" value="1" />
									<div class="panel-body">
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-4">
												<div class="form-group">
													<label>Position du menu</label>
													<input type="number" class="form-control" name="menu_edit_position" value="<?php $menu_position = new Menu(); $menu_position->getMenuDBID($id); echo $menu_position->getPosition(); ?>" required />
												</div>
											</div>
											<div class="col-xs-12 col-sm-12 col-md-8">
												<div class="form-group">
													<label>Nom du menu</label>
													<input type="text" class="form-control" name="menu_edit_name" value="<?php $menu_name = new Menu(); $menu_name->getMenuDBID($id); echo $menu_name->getName(); ?>" required />
												</div>
											</div>
										</div>
										<div class="form-group">
											<label>Nom de la table</label>
											<input type="text" class="form-control" name="menu_edit_table" value="<?php $menu_name_table = new Menu(); $menu_name_table->getMenuDBID($id); echo $menu_name_table->getNametable(); ?>" required />
										</div>
										<div class="form-group">
											<label>Nom de l'icône</label>
											<div class="input-group">
												<input type="text" class="form-control" name="menu_edit_icon" value="<?php $menu_icon = new Menu(); $menu_icon->getMenuDBID($id); echo $menu_icon->getIcon(); ?>" id="menuIcon" required />
												<div class="input-group-btn">
													<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><i class="fa fa-info"></i></button>
													<ul class="dropdown-menu">
														<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='align-justify'"><i class="fa fa-align-justify"></i> align-justify</a></li>
														<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='book'"><i class="fa fa-book"></i> book</a></li>
														<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='film'"><i class="fa fa-film"></i> film</a></li>
														<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='gamepad'"><i class="fa fa-gamepad"></i> gamepad</a></li>
														<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='music'"><i class="fa fa-music"></i> music</a></li>
													</ul>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label>Nom de la catégorie</label>
											<select class="form-control chosen" name="menu_edit_category">
												<option></option>
												<?php
													$menu_id_category = new Menu();
													$menu_id_category->getMenuDBID($id);
													$menu_id_category = $menu_id_category->getIDcategory();
													
													$category = new Category();
													$liste_category = $category->getCategoryList();
													foreach ($liste_category as $category => $val_category)
													{
														if ($val_category['id'] == $menu_id_category) $selected = "selected"; else $selected = "";
														echo "<option value=\"".$val_category['id']."\" ".$selected.">".$val_category['name']."</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Modifier</button></div>
								</form>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
		<?php include('./template/bootstrap/includes/footer.php'); ?>
		<?php include('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>