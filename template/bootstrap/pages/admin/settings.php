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
			$category->setPosition(1);
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
		if (!empty($_['category_edit_position']) && !empty($_['category_edit_name']))
		{
			$category = new Category();
			$category->getCategoryDBID($id);
			$category->setName($_['category_edit_name']);
			$category->setPosition($_['category_edit_position']);
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
			$test[$lib_errors][] = "Il est nécessaire de fournir le nom et l'icône du menu, ainsi que le nom de la table.";
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
			$test[$lib_errors][] = "Il est nécessaire de fournir la position, le nom et l'icône du menu, le nom de la table, ainsi que la catégorie.";
		}
	}

	/*
		=================================
		DETAIL -> AJOUT
		=================================
	*/
	if (isset($_['detailAddButton']) && $_['detailAddButton'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_['detail_add_name']) && !empty($_['detail_add_type']) && !empty($_['detail_add_icon']))
		{
			$detail = new Detail();
			$detail->setName($_['detail_add_name']);
			$detail->setType($_['detail_add_type']);
			$detail->setIcon($_['detail_add_icon']);
			$detail->setIdmenu($id);
			$detail->saveDetail();
		} else {
			$test[$lib_errors][] = "Il est nécessaire de fournir le nom, le type et l'icône du menu.";
		}
	}

	/*
		=================================
		DETAIL -> MODIFICATION
		=================================
	*/
	if (isset($_['detailEditButton']) && $_['detailEditButton'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_['detail_edit_name']) && !empty($_['detail_edit_type']) && !empty($_['detail_edit_icon']))
		{
			$detail = new Detail();
			$detail->getDetailDBID($id);
			$detail->setName($_['detail_edit_name']);
			$detail->setIcon($_['detail_edit_icon']);
			$detail->setType($_['detail_edit_type']);
			if (!empty($_['detail_edit_options'])) $detail->setOptions($_['detail_edit_options']);
			$detail->saveDetail();
		} else {
			$test[$lib_errors][] = "Il est nécessaire de fournir le nom, le type et l'icône du menu.";
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
					<?php if ($type == "category_add") { ?>
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
					<?php if ($type == "category_edit") { ?>
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
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-4">
												<div class="form-group">
													<label>Position de la catégorie</label>
													<input type="number" class="form-control" name="category_edit_position" value="<?php $category_position = new Category(); $category_position->getCategoryDBID($id); echo $category_position->getPosition(); ?>" required />
												</div>
											</div>
											<div class="col-xs-12 col-sm-12 col-md-8">
												<div class="form-group">
													<label>Nom de la catégorie</label>
													<input type="text" class="form-control" name="category_edit_name" value="<?php $catgory_name = new Category(); $catgory_name->getCategoryDBID($id); echo $catgory_name->getName(); ?>" required />
												</div>
											</div>
										</div>
									</div>
									<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Modifier</button></div>
								</form>
							</div>
						</div>
					<?php } ?>
					<?php if ($type == "menu_add") { ?>
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
					<?php if ($type == "menu_edit") { ?>
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
			<?php if ($tab == 3) { ?>
				<!-- SUPPRESSION D'UN MENU -->
				<div class="modal fade" id="ConfirmSupprDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header"><h4 class="modal-title" id="myModalLabel">Supprimer un menu</h4></div>
							<div class="modal-body">
								<p>Voulez-vous vraiment supprimer ce menu ?</p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
								<button type="button" class="btn btn-primary" onclick="delDetail()" data-dismiss="modal">Oui</button>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-4">
						<div class="panel panel-default">
							<div class="panel-heading">Listes + Détails</div>
							<table class="table table-bordered">
								<?php
									$category = new Category();
									$liste_category = $category->getCategoryList();
								?>
								<?php foreach ($liste_category as $category => $val_category) { ?>
									<tr>
										<td style="width:96%;background-color:#f9f9f9;" colspan="4"><?php echo $val_category['name']; ?></td>
									</tr>
									<?php
										$menu = new Menu();
										$liste_menu = $menu->getMenuDBIDCategory($val_category['id']);
									?>
									<?php foreach ($liste_menu as $menu => $val_menu) { ?>
										<tr>
											<td><i class="fa fa-<?php echo $val_menu['icon']; ?>"></i></td>
											<td style="width:89%;"><?php echo $val_menu['name']; ?></td>
											<td class="text-center"><a href="./?op=settings&tab=3&type=list_settings&id=<?php echo $val_menu['id']; ?>" class="btn btn-info btn-xs" title="Modifier la page liste"><i class="fa fa-pencil"></i></a></td>
											<td class="text-center"><a href="./?op=settings&tab=3&type=detail_settings&id=<?php echo $val_menu['id']; ?>" class="btn btn-info btn-xs" title="Modifier la page détail"><i class="fa fa-pencil"></i></a></td>
										</tr>
									<?php } ?>
								<?php } ?>
							</table>
						</div>
						<?php if ($type == "detail_settings") { ?>
							<div class="panel panel-default">
								<div class="panel-heading">Ajouter un menu</div>
								<form method="post" action="?op=settings&tab=3&type=detail_settings&id=<?php echo $id; ?>" id="detailAddForm">
									<input type="hidden" name="detailAddButton" value="1">
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
											<label>Nom du menu</label>
											<input type="text" class="form-control" name="detail_add_name" required />
											<span class="form-control-feedback"><i class="fa fa-pencil"></i></span>
										</div>
										<div class="form-group">
											<label>Type du menu</label>
											<select class="form-control chosen" name="detail_add_type">
												<option value="acteurs">Acteurs</option>
												<option value="annee">Année</option>
												<option value="audio">Audio</option>
												<option value="bande_annonce">Bande-annonce</option>
												<option value="bonus">Bonus</option>
												<option value="commentaires">Commentaires</option>
												<option value="duree">Durée</option>
												<option value="edition">Edition</option>
												<option value="entree_date">Date d'entrée</option>
												<option value="fichier">Fichier</option>
												<option value="film_vu">Film Vu</option>
												<option value="genre">Genre</option>
												<option value="nombre_support">Nombre de supports</option>
												<option value="note">Note</option>
												<option value="pays">Pays</option>
												<option value="realisateurs">Réalisateurs</option>
												<option value="reference">Référence</option>
												<option value="sous_titres">Sous-titres</option>
												<option value="support">Support</option>
												<option value="synopsis">Synopsis</option>
												<option value="titre_vo">Titre VO</option>
												<option value="zone">Zone</option>
											</select>
										</div>
										<div class="form-group">
											<label>Nom de l'icône</label>
											<select class="form-control icon-select" name="detail_add_icon">
												<option value="align-justify" data-icon="fa-align-justify">align-justify</option>
												<option value="barcode" data-icon="fa-barcode">barcode</option>
												<option value="bars" data-icon="fa-bars">bars</option>
												<option value="book" data-icon="fa-book">book</option>
												<option value="calendar" data-icon="fa-calendar">calendar</option>
												<option value="cc" data-icon="fa-cc">cc</option>
												<option value="clock-o" data-icon="fa-clock-o">clock-o</option>
												<option value="comments" data-icon="fa-comments">comments</option>
												<option value="dot-circle-o" data-icon="fa-dot-circle-o">dot-circle-o</option>
												<option value="eye-slash" data-icon="fa-eye-slash">eye-slash</option>
												<option value="film" data-icon="fa-film">film</option>
												<option value="flag" data-icon="fa-flag">flag</option>
												<option value="gamepad" data-icon="fa-gamepad">gamepad</option>
												<option value="globe" data-icon="fa-globe">globe</option>
												<option value="inbox" data-icon="fa-inbox">inbox</option>
												<option value="info-circle" data-icon="fa-info-circle">info-circle</option>
												<option value="music" data-icon="fa-music">music</option>
												<option value="pencil" data-icon="fa-pencil">pencil</option>
												<option value="play" data-icon="fa-play">play</option>
												<option value="tag" data-icon="fa-tag">tag</option>
												<option value="tasks" data-icon="fa-tasks">tasks</option>
												<option value="thumbs-o-up" data-icon="fa-thumbs-o-up">thumbs-o-up</option>
												<option value="user" data-icon="fa-user">user</option>
												<option value="users" data-icon="fa-users">users</option>
											</select>
										</div>
									</div>
									<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Ajouter</button></div>
								</form>
							</div>
						<?php } ?>
					</div>
					<?php if ($type == "detail_settings") { ?>
						<?php
							// TitreVO
							$detail_TitreVO = new Detail();
							$detail_TitreVO->getDetailList("titre_vo", $id);

							// TitreVO
							$detail_Genre = new Detail();
							$detail_Genre->getDetailList("genre", $id);
							
							// Année
							$detail_Annee = new Detail();
							$detail_Annee->getDetailList("annee", $id);

							// Durée
							$detail_Duree = new Detail();
							$detail_Duree->getDetailList("duree", $id);

							// Pays
							$detail_Pays = new Detail();
							$detail_Pays->getDetailList("pays", $id);

							// Note
							$detail_Note = new Detail();
							$detail_Note->getDetailList("note", $id);

							// FilmVu
							$detail_FilmVu = new Detail();
							$detail_FilmVu->getDetailList("film_vu", $id);

							// Bande anonce
							$detail_BA = new Detail();
							$detail_BA->getDetailList("bande_annonce", $id);

							// Fichier
							$detail_Fichier = new Detail();
							$detail_Fichier->getDetailList("fichier", $id);

							// Synopsis
							$detail_Synopsis = new Detail();
							$detail_Synopsis->getDetailList("synopsis", $id);

							// Realisateurs
							$detail_Realisateurs = new Detail();
							$detail_Realisateurs->getDetailList("realisateurs", $id);

							// Acteurs
							$detail_Acteurs = new Detail();
							$detail_Acteurs->getDetailList("acteurs", $id);

							// Bonus
							$detail_Bonus = new Detail();
							$detail_Bonus->getDetailList("bonus", $id);

							// Support
							$detail_Support = new Detail();
							$detail_Support->getDetailList("support", $id);

							// Edition
							$detail_Edition = new Detail();
							$detail_Edition->getDetailList("edition", $id);

							// Reference
							$detail_Reference = new Detail();
							$detail_Reference->getDetailList("reference", $id);

							// Entree date
							$detail_EntreeDate = new Detail();
							$detail_EntreeDate->getDetailList("entree_date", $id);

							// Nombre support
							$detail_NombreSupport = new Detail();
							$detail_NombreSupport->getDetailList("nombre_support", $id);

							// Zone
							$detail_Zone = new Detail();
							$detail_Zone->getDetailList("zone", $id);

							// Audio
							$detail_Audio = new Detail();
							$detail_Audio->getDetailList("audio", $id);

							// Sous titres
							$detail_SousTitres = new Detail();
							$detail_SousTitres->getDetailList("sous_titres", $id);
						?>
						<div class="col-xs-12 col-sm-12 col-md-8">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-8 text-center">
									<div class="detail"><img data-src="holder.js/100px100p?text=aucune \n image" alt="Affiche" /></div>
									<br />
								</div>
								<div class="col-xs-12 col-sm-12 col-md-4">
									<div class="panel">
										<li class="list-group-item"><i class="fa fa-pencil"></i> Titre VF</li>
										<?php if (!empty($detail_TitreVO->getType())) { ?>
											<li class="list-group-item">
												<table style="width:100%">
													<tr>
														<td style="width:78%"><i class="fa fa-<?php echo $detail_TitreVO->getIcon(); ?>"></i> <?php echo $detail_TitreVO->getName(); ?></td>
														<td class="text-center">
															<div class="btn-group">
																<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_TitreVO->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_TitreVO->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
															</div>
														</td>
													</tr>
												</table>
											</li>
										<?php } ?>
										<?php if (!empty($detail_Genre->getType())) { ?>
											<li class="list-group-item">
												<table style="width:100%">
													<tr>
														<td style="width:78%"><i class="fa fa-<?php echo $detail_Genre->getIcon(); ?>"></i> <?php echo $detail_Genre->getName(); ?></td>
														<td class="text-center">
															<div class="btn-group">
																<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Genre->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Genre->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
															</div>
														</td>
													</tr>
												</table>
											</li>
										<?php } ?>
										<?php if (!empty($detail_Annee->getType()) || !empty($detail_Duree->getType()) || !empty($detail_Pays->getType())) { ?>
											<li class="list-group-item">
												<?php if (!empty($detail_Annee->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><i class="fa fa-<?php echo $detail_Annee->getIcon(); ?>"></i> <?php echo $detail_Annee->getName(); ?></td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Annee->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Annee->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
												<?php if (!empty($detail_Duree->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><i class="fa fa-<?php echo $detail_Duree->getIcon(); ?>"></i> <?php echo $detail_Duree->getName(); ?></td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Duree->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Duree->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
												<?php if (!empty($detail_Pays->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><i class="fa fa-<?php echo $detail_Pays->getIcon(); ?>"></i> <?php echo $detail_Pays->getName(); ?></td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Pays->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Pays->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
											</li>
										<?php } ?>
										<?php if (!empty($detail_Note->getType())) { ?>
											<li class="list-group-item">
												<table style="width:100%">
													<tr>
														<td style="width:78%"><i class="fa fa-<?php echo $detail_Note->getIcon(); ?>"></i> <?php echo $detail_Note->getName(); ?></td>
														<td class="text-center">
															<div class="btn-group">
																<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Note->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Note->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
															</div>
														</td>
													</tr>
												</table>
											</li>
										<?php } ?>
										<?php if (!empty($detail_FilmVu->getType()) || !empty($detail_BA->getType()) || !empty($detail_Fichier->getType())) { ?>
											<li class="list-group-item">
												<?php if (!empty($detail_FilmVu->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><div class="btn btn-danger btn-xs" disabled="disabled"><i class="fa fa-<?php echo $detail_FilmVu->getIcon(); ?>"></i> <?php echo $detail_FilmVu->getName(); ?></div></td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_FilmVu->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_FilmVu->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
												<?php if (!empty($detail_BA->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><div class="btn btn-default btn-xs" disabled="disabled"><i class="fa fa-<?php echo $detail_BA->getIcon(); ?>"></i> <?php echo $detail_BA->getName(); ?></div></td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_BA->getId(); ?>" class="btn btn-info btn-xs" title="Modifier la page détail"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_BA->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
												<?php if (!empty($detail_Fichier->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><div class="btn btn-default btn-xs" disabled="disabled"><i class="fa fa-<?php echo $detail_Fichier->getIcon(); ?>"></i> <?php echo $detail_Fichier->getName(); ?></div></td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Fichier->getId(); ?>" class="btn btn-info btn-xs" title="Modifier la page détail"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Fichier->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
											</li>
										<?php } ?>
									</div>
								</div>		
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-8">
									<?php if (!empty($detail_Synopsis->getType())) { ?>
										<div class="panel panel-default">
											<div class="panel-heading clearfix">
												<h3 class="panel-title pull-left" style="padding-top: 2px;"><i class="fa fa-<?php echo $detail_Synopsis->getIcon(); ?>"></i> <?php echo $detail_Synopsis->getName(); ?></h3>
												<div class="btn-group pull-right">
													<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Synopsis->getId(); ?>" class="btn btn-info btn-xs" title="Modifier la page détail"><i class="fa fa-pencil"></i></a>
													<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Synopsis->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
												</div>
											</div>
											<div class="panel-body"></div>
										</div>
									<?php } ?>
									<?php if (!empty($detail_Realisateurs->getType())) { ?>
										<div class="panel panel-default">
											<div class="panel-heading clearfix">
												<h3 class="panel-title pull-left" style="padding-top: 2px;"><i class="fa fa-<?php echo $detail_Realisateurs->getIcon(); ?>"></i> <?php echo $detail_Realisateurs->getName(); ?></h3>
												<div class="btn-group pull-right">
													<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Realisateurs->getId(); ?>" class="btn btn-info btn-xs" title="Modifier la page détail"><i class="fa fa-pencil"></i></a>
													<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Realisateurs->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
												</div>
											</div>
											<div class="panel-body"></div>
										</div>
									<?php } ?>
									<?php if (!empty($detail_Acteurs->getType())) { ?>
										<div class="panel panel-default">
											<div class="panel-heading clearfix">
												<h3 class="panel-title pull-left" style="padding-top: 2px;"><i class="fa fa-<?php echo $detail_Acteurs->getIcon(); ?>"></i> <?php echo $detail_Acteurs->getName(); ?></h3>
												<div class="btn-group pull-right">
													<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Acteurs->getId(); ?>" class="btn btn-info btn-xs" title="Modifier la page détail"><i class="fa fa-pencil"></i></a>
													<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Acteurs->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
												</div>
											</div>
											<div class="panel-body"></div>
										</div>
									<?php } ?>
									<?php if (!empty($detail_Bonus->getType())) { ?>
										<div class="panel panel-default">
											<div class="panel-heading clearfix">
												<h3 class="panel-title pull-left" style="padding-top: 2px;"><i class="fa fa-<?php echo $detail_Bonus->getIcon(); ?>"></i> <?php echo $detail_Bonus->getName(); ?></h3>
												<div class="btn-group pull-right">
													<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Bonus->getId(); ?>" class="btn btn-info btn-xs" title="Modifier la page détail"><i class="fa fa-pencil"></i></a>
													<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Bonus->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
												</div>
											</div>
											<div class="panel-body"></div>
										</div>
									<?php } ?>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-4">
									<?php if (!empty($detail_Support->getType()) || !empty($detail_Edition->getType()) || !empty($detail_Reference->getType()) || !empty($detail_EntreeDate->getType()) || !empty($detail_NombreSupport->getType()) || !empty($detail_Zone->getType())) { ?>
										<div class="panel panel-default">
											<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-paperclip"></i> Détails</h3></div>
											<div class="panel-body">
												<?php if (!empty($detail_Support->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><i class="fa fa-<?php echo $detail_Support->getIcon(); ?>"></i> <strong><?php echo $detail_Support->getName(); ?> : </strong</td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Support->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Support->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
												<?php if (!empty($detail_Edition->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><i class="fa fa-<?php echo $detail_Edition->getIcon(); ?>"></i> <strong><?php echo $detail_Edition->getName(); ?> : </strong</td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Edition->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Edition->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
												<?php if (!empty($detail_Reference->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><i class="fa fa-<?php echo $detail_Reference->getIcon(); ?>"></i> <strong><?php echo $detail_Reference->getName(); ?> : </strong</td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Reference->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Reference->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
												<?php if (!empty($detail_EntreeDate->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><i class="fa fa-<?php echo $detail_EntreeDate->getIcon(); ?>"></i> <strong><?php echo $detail_EntreeDate->getName(); ?> : </strong</td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_EntreeDate->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_EntreeDate->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
												<?php if (!empty($detail_NombreSupport->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><i class="fa fa-<?php echo $detail_NombreSupport->getIcon(); ?>"></i> <strong><?php echo $detail_NombreSupport->getName(); ?> : </strong</td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_NombreSupport->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_NombreSupport->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
												<?php if (!empty($detail_Zone->getType())) { ?>
													<table style="width:100%">
														<tr>
															<td style="width:78%"><i class="fa fa-<?php echo $detail_Zone->getIcon(); ?>"></i> <strong><?php echo $detail_Zone->getName(); ?> : </strong</td>
															<td class="text-center">
																<div class="btn-group">
																	<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Zone->getId(); ?>" class="btn btn-info btn-xs" title="Modifier le menu"><i class="fa fa-pencil"></i></a>
																	<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Zone->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
																</div>
															</td>
														</tr>
													</table>
												<?php } ?>
											</div>
										</div>
									<?php } ?>
									<?php if (!empty($detail_Audio->getType())) { ?>
										<div class="panel panel-default">
											<div class="panel-heading clearfix">
												<h3 class="panel-title pull-left" style="padding-top: 2px;"><i class="fa fa-<?php echo $detail_Audio->getIcon(); ?>"></i> <?php echo $detail_Audio->getName(); ?></h3>
												<div class="btn-group pull-right">
													<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_Audio->getId(); ?>" class="btn btn-info btn-xs" title="Modifier la page détail"><i class="fa fa-pencil"></i></a>
													<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_Audio->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
												</div>
											</div>
											<div class="panel-body"></div>
										</div>
									<?php } ?>
									<?php if (!empty($detail_SousTitres->getType())) { ?>
										<div class="panel panel-default">
											<div class="panel-heading clearfix">
												<h3 class="panel-title pull-left" style="padding-top: 2px;"><i class="fa fa-<?php echo $detail_SousTitres->getIcon(); ?>"></i> <?php echo $detail_SousTitres->getName(); ?></h3>
												<div class="btn-group pull-right">
													<a href="./?op=settings&tab=3&type=detail_edit&id=<?php echo $detail_SousTitres->getId(); ?>" class="btn btn-info btn-xs" title="Modifier la page détail"><i class="fa fa-pencil"></i></a>
													<button class="btn btn-danger btn-xs" onclick="detail_del(<?php echo $detail_SousTitres->getId(); ?>)" title="Supprimer le menu"><i class="fa fa-trash-o"></i></button>
												</div>
											</div>
											<div class="panel-body"></div>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php } ?>
					<?php if ($type == "detail_edit") { ?>
						<div class="col-xs-12 col-sm-12 col-md-8">
							<div class="panel panel-default">
								<div class="panel-heading">Modifier un menu</div>
								<form method="post" action="./?op=settings&tab=3&type=detail_edit&id=<?php echo $id; ?>" id="detailEditForm">
									<input type="hidden" name="detailEditButton" value="1" />
									<div class="panel-body">
										<div class="form-group">
											<label>Nom du menu</label>
											<input type="text" class="form-control" name="detail_edit_name" value="<?php $detail_name = new Detail(); $detail_name->getDetailDBID($id); echo $detail_name->getName(); ?>" required />
										</div>
										<div class="form-group">
											<label>Type du menu</label>
											<select class="form-control chosen" name="detail_edit_type">
												<?php
													$detail_type = new Detail();
													$detail_type->getDetailDBID($id);
												?>
												<option value="acteurs" <?php if ($detail_type->getType() == "acteurs") echo "selected"; ?>>Acteurs</option>
												<option value="annee" <?php if ($detail_type->getType() == "annee") echo "selected"; ?>>Année</option>
												<option value="audio" <?php if ($detail_type->getType() == "audio") echo "selected"; ?>>Audio</option>
												<option value="bande_annonce" <?php if ($detail_type->getType() == "bande_annonce") echo "selected"; ?>>Bande-annonce</option>
												<option value="bonus" <?php if ($detail_type->getType() == "bonus") echo "selected"; ?>>Bonus</option>
												<option value="commentaires" <?php if ($detail_type->getType() == "commentaires") echo "selected"; ?>>Commentaires</option>
												<option value="duree" <?php if ($detail_type->getType() == "duree") echo "selected"; ?>>Durée</option>
												<option value="edition" <?php if ($detail_type->getType() == "edition") echo "selected"; ?>>Edition</option>
												<option value="entree_date" <?php if ($detail_type->getType() == "entree_date") echo "selected"; ?>>Date d'entrée</option>
												<option value="fichier" <?php if ($detail_type->getType() == "fichier") echo "selected"; ?>>Fichier</option>
												<option value="film_vu" <?php if ($detail_type->getType() == "film_vu") echo "selected"; ?>>Film Vu</option>
												<option value="genre" <?php if ($detail_type->getType() == "genre") echo "selected"; ?>>Genre</option>
												<option value="nombre_support" <?php if ($detail_type->getType() == "nombre_support") echo "selected"; ?>>Nombre de supports</option>
												<option value="note" <?php if ($detail_type->getType() == "note") echo "selected"; ?>>Note</option>
												<option value="pays" <?php if ($detail_type->getType() == "pays") echo "selected"; ?>>Pays</option>
												<option value="realisateurs" <?php if ($detail_type->getType() == "realisateurs") echo "selected"; ?>>Réalisateurs</option>
												<option value="reference" <?php if ($detail_type->getType() == "reference") echo "selected"; ?>>Référence</option>
												<option value="sous_titres" <?php if ($detail_type->getType() == "sous_titres") echo "selected"; ?>>Sous-titres</option>
												<option value="support" <?php if ($detail_type->getType() == "support") echo "selected"; ?>>Support</option>
												<option value="synopsis" <?php if ($detail_type->getType() == "synopsis") echo "selected"; ?>>Synopsis</option>
												<option value="titre_vo" <?php if ($detail_type->getType() == "titre_vo") echo "selected"; ?>>Titre VO</option>
												<option value="zone" <?php if ($detail_type->getType() == "zone") echo "selected"; ?>>Zone</option>
											</select>
										</div>
										<div class="form-group">
											<label>Nom de l'icone</label>
											<select class="form-control icon-select" name="detail_edit_icon">
												<?php
													$detail_icon = new Detail();
													$detail_icon->getDetailDBID($id);
												?>
												<option value="align-justify" data-icon="fa-align-justify" <?php if ($detail_icon->getIcon() == "align-justify") echo "selected"; ?>>align-justify</option>
												<option value="barcode" data-icon="fa-barcode" <?php if ($detail_icon->getIcon() == "barcode") echo "selected"; ?>>barcode</option>
												<option value="bars" data-icon="fa-bars" <?php if ($detail_icon->getIcon() == "bars") echo "selected"; ?>>bars</option>
												<option value="book" data-icon="fa-book" <?php if ($detail_icon->getIcon() == "book") echo "selected"; ?>>book</option>
												<option value="calendar" data-icon="fa-calendar" <?php if ($detail_icon->getIcon() == "calendar") echo "selected"; ?>>calendar</option>
												<option value="cc" data-icon="fa-cc" <?php if ($detail_icon->getIcon() == "cc") echo "selected"; ?>>cc</option>
												<option value="clock-o" data-icon="fa-clock-o" <?php if ($detail_icon->getIcon() == "clock-o") echo "selected"; ?>>clock-o</option>
												<option value="comments" data-icon="fa-comments" <?php if ($detail_icon->getIcon() == "comments") echo "selected"; ?>>comments</option>
												<option value="dot-circle-o" data-icon="fa-dot-circle-o" <?php if ($detail_icon->getIcon() == "dot-circle-o") echo "selected"; ?>>dot-circle-o</option>
												<option value="eye-slash" data-icon="fa-eye-slash" <?php if ($detail_icon->getIcon() == "eye-slash") echo "selected"; ?>>eye-slash</option>
												<option value="film" data-icon="fa-film" <?php if ($detail_icon->getIcon() == "film") echo "selected"; ?>>film</option>
												<option value="flag" data-icon="fa-flag" <?php if ($detail_icon->getIcon() == "flag") echo "selected"; ?>>flag</option>
												<option value="gamepad" data-icon="fa-gamepad" <?php if ($detail_icon->getIcon() == "gamepad") echo "selected"; ?>>gamepad</option>
												<option value="globe" data-icon="fa-globe" <?php if ($detail_icon->getIcon() == "globe") echo "selected"; ?>>globe</option>
												<option value="inbox" data-icon="fa-inbox" <?php if ($detail_icon->getIcon() == "inbox") echo "selected"; ?>>inbox</option>
												<option value="info-circle" data-icon="fa-info-circle" <?php if ($detail_icon->getIcon() == "info-circle") echo "selected"; ?>>info-circle</option>
												<option value="music" data-icon="fa-music" <?php if ($detail_icon->getIcon() == "music") echo "selected"; ?>>music</option>
												<option value="pencil" data-icon="fa-pencil" <?php if ($detail_icon->getIcon() == "pencil") echo "selected"; ?>>pencil</option>
												<option value="play" data-icon="fa-play" <?php if ($detail_icon->getIcon() == "play") echo "selected"; ?>>play</option>
												<option value="tag" data-icon="fa-tag" <?php if ($detail_icon->getIcon() == "tag") echo "selected"; ?>>tag</option>
												<option value="tasks" data-icon="fa-tasks" <?php if ($detail_icon->getIcon() == "tasks") echo "selected"; ?>>tasks</option>
												<option value="thumbs-o-up" data-icon="fa-thumbs-o-up" <?php if ($detail_icon->getIcon() == "thumbs-o-up") echo "selected"; ?>>thumbs-o-up</option>
												<option value="user" data-icon="fa-user" <?php if ($detail_icon->getIcon() == "user") echo "selected"; ?>>user</option>
												<option value="users" data-icon="fa-users" <?php if ($detail_icon->getIcon() == "users") echo "selected"; ?>>users</option>
											</select>
										</div>
										<?php if ($detail_type->getType() == "duree") { ?>
											<div class="form-group">
												<label>Option du menu</label>
												<div class="radio">
													<label><input type="radio" name="detail_edit_options" value="pages" <?php $detail_options = new Detail(); $detail_options->getDetailDBID($id); if ($detail_options->getOptions() == "pages") echo 'checked'; ?>> .. pages</label>
												</div>
												<div class="radio">
													<label><input type="radio" name="detail_edit_options" value="temps" <?php $detail_options = new Detail(); $detail_options->getDetailDBID($id); if ($detail_options->getOptions() == "temps") echo 'checked'; ?>> ..h ..mins</label>
												</div>
											</div>
										<?php } ?>
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