<?php
	if (!isset($_SESSION['username']) || $user['rank'] != '3')
	{
		header('location: ./');
		exit();
	}

	// Valeurs par défaut, remplacées si une autre valeur est saisie
	foreach (array('optionsTitle', 'optionsOpen', 'optionsAvatarMaxWidth', 'optionsAvatarMaxHeight', 'optionsAvatarMaxWeight', 'optionsLastaddMax', 'membersUsername', 'membersMail', 'membersPassword1', 'membersPassword2', 'categoryName', 'menuName', 'menuTable', 'menuIcon', 'menuFilterName', 'menuFilterType') as $var)
	{
		if (!empty($_[$var]))
		{
			$$var = $_[$var];
		} else {
			$$var = '';
		}
	}

	/*
		=================================
		OPTIONS
		=================================
	*/
	if (empty($_['optionsTitle']))
	{
		$optionsTitle = $config['title'];
	}
	if (empty($_['optionsAvatarMaxWidth']))
	{
		$optionsAvatarMaxWidth = $config['avatarMaxWidth'];
	}
	if (empty($_['optionsAvatarMaxHeight']))
	{
		$optionsAvatarMaxHeight = $config['avatarMaxHeight'];
	}
	if (empty($_['optionsAvatarMaxWeight']))
	{
		$optionsAvatarMaxWeight = $config['avatarMaxWeight'];
	}
	if (empty($_['optionsLastaddMax']))
	{
		$optionsLastaddMax = $config['lastaddMax'];
	}

	if (isset($_['optionsButton']))
	{
		$query = $db->prepare('UPDATE `site_configuration` SET `value` = :value WHERE `key` = "open"');
		$query->bindValue(':value', $_['optionsOpen'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();

		$query = $db->prepare('UPDATE `site_configuration` SET `value` = :value WHERE `key` = "title"');
		$query->bindValue(':value', $_['optionsTitle'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();

		$query = $db->prepare('UPDATE `site_configuration` SET `value` = :value WHERE `key` = "avatarMaxWidth"');
		$query->bindValue(':value', $_['optionsAvatarMaxWidth'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();

		$query = $db->prepare('UPDATE `site_configuration` SET `value` = :value WHERE `key` = "avatarMaxHeight"');
		$query->bindValue(':value', $_['optionsAvatarMaxHeight'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();

		$query = $db->prepare('UPDATE `site_configuration` SET `value` = :value WHERE `key` = "avatarMaxWeight"');
		$query->bindValue(':value', $_['optionsAvatarMaxWeight'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();

		$query = $db->prepare('UPDATE `site_configuration` SET `value` = :value WHERE `key` = "lastaddMax"');
		$query->bindValue(':value', $_['optionsLastaddMax'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		MEMBRES -> INFORMATIONS
		=================================
	*/
	$settings_members_query = $db->prepare('SELECT `id`, `username`, `mail`, `avatar`, `rank`, `access` FROM `site_user` ORDER BY `rank` DESC, `id`');
	$settings_members_query->execute();

	/*
		=================================
		MEMBRES -> AJOUT
		=================================
	*/
	if (isset($_['membersAddButton']))
	{
		// Vérification que tous les champs sont renseignés
		if (empty($_['membersUsername']) || empty($_['membersMail']) || empty($_['membersPassword1']) || empty($_['membersPassword2']))
		{
			$membersMessage = 'Veuillez renseigner un nom d\'utilisateur, un email et un mot de passe.';
			$i++;
		}

		require_once('./includes/mysqlConstants.php');
		require_once('./includes/mysqlConnector.php');

		$query = $db->prepare('SELECT `username` FROM `site_user` WHERE `username` = :username');
		$query->bindValue(':username', $_['membersUsername'], PDO::PARAM_STR);
		$query->execute();
		$membersVerifUsername = $query->fetch();
		$query->CloseCursor();

		// Vérification si le nom d'utilisateur est présent dans la table site_user
		if ($membersVerifUsername['username'] == $_['membersUsername'] && $i == 0)
		{
			$membersMessageUsername = 'Ce nom d\'utilisateur fait déjà l\'objet d\'un compte enregistré.';
			$i++;
		}

		$query = $db->prepare('SELECT `mail` FROM `site_user` WHERE `mail` = :mail');
		$query->bindValue(':mail', $_['membersMail'], PDO::PARAM_STR);
		$query->execute();
		$membersVerifMail = $query->fetch();
		$query->CloseCursor();

		// Vérification si l'email est présent dans la table site_user
		if ($membersVerifMail['mail'] == $_['membersMail'] && $i == 0)
		{
			$membersMessageMail = 'Cette adresse email fait déjà l\'objet d\'un compte enregistré.';
			$i++;
		}

		// Vérification des 2 mots de passe
		if ($_['membersPassword1'] != $_['membersPassword2'] && $i == 0)
		{
			$membersMessagePassword = 'Les mots de passe ne correspondent pas.';
			$i++;
		}
	}

	// Pas d'erreur, on inscrit le membre
	if (isset($_['membersAddButton']) && $i == 0)
	{
		$query = $db->prepare('INSERT INTO `site_user` (`username`, `password`, `mail`, `date_registration`, `url_website`, `url_facebook`, `url_twitter`, `url_googleplus`, `country`, `rank`) VALUES (:username, :password, :mail, :date_registration, :url_website, :url_facebook, :url_twitter, :url_googleplus, :country, :rank)');
		$query->bindValue(':username', $_['membersUsername'], PDO::PARAM_STR);
		$query->bindValue(':password', md5($_['membersPassword1']), PDO::PARAM_STR);
		$query->bindValue(':mail', $_['membersMail'], PDO::PARAM_STR);
		$query->bindValue(':date_registration', date('Y-m-d'), PDO::PARAM_INT);
		$query->bindValue(':url_website', '', PDO::PARAM_STR);
		$query->bindValue(':url_facebook', '', PDO::PARAM_STR);
		$query->bindValue(':url_twitter', '', PDO::PARAM_STR);
		$query->bindValue(':url_googleplus', '', PDO::PARAM_STR);
		$query->bindValue(':country', '', PDO::PARAM_STR);
		$query->bindValue(':rank', $_['membersRank'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		MEMBRES -> MODIFICATION
		=================================
	*/
	// Modifier le rang d'un membre
	if (isset($_['membersRankSelect']))
	{
		$query = $db->prepare('UPDATE `site_user` SET `rank` = :rank WHERE `id` = :id');
		$query->bindValue(':rank', $_['membersRankSelect'], PDO::PARAM_STR);
		$query->bindValue(':id', $_['membersId'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	// Modifier l'accès d'un membre
	if (isset($_['membersAccessButton']))
	{
		if ($_['membersAccess'] == '0')
		{
			$query = $db->prepare('UPDATE `site_user` SET `access` = :access WHERE `id` = :id');
			$query->bindValue(':access', '1', PDO::PARAM_STR);
			$query->bindValue(':id', $_['membersId'], PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();
		} else {
			$query = $db->prepare('UPDATE `site_user` SET `access` = :access WHERE `id` = :id');
			$query->bindValue(':access', '0', PDO::PARAM_STR);
			$query->bindValue(':id', $_['membersId'], PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();
		}

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		MEMBRES -> SUPPRESSION
		=================================
	*/
	if (isset($_['memberDell']))
	{
		$query = $db->prepare('DELETE FROM `site_user` WHERE `id` = :id');
		$query->bindValue(':id', $_['memberDell'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		$query = $db->query('ALTER TABLE `site_user` AUTO_INCREMENT = 1');

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		CATEGORIE -> INFORMATIONS
		=================================
	*/
	$settings_category_query = $db->prepare('SELECT `id`, `name` FROM `site_category`');
	$settings_category_query->execute();

	/*
		=================================
		CATEGORIE -> AJOUT
		=================================
	*/
	if (isset($_['categoryAddButton']))
	{
		// Vérification que le champ est renseigné
		if (empty($_['categoryName']))
		{
			$categoryMessage = 'Veuillez renseigner le nom de la catégorie.';
			$i++;
		}
	}

	// Pas d'erreur, on ajoute la catégorie
	if (isset($_['categoryAddButton']) && $i == 0)
	{
		$query = $db->prepare('INSERT INTO `site_category` (`name`) VALUES (:name)');
		$query->bindValue(':name', $_['categoryName'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		CATEGORIE -> MODIFICATION
		=================================
	*/
	if (isset($_['categoryEditButton']))
	{
		$query = $db->prepare('UPDATE `site_category` SET `name` = :name WHERE `id` = :id');
		$query->bindValue(':name', $_['categoryEditName'], PDO::PARAM_STR);
		$query->bindValue(':id', $_['categoryEditId'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		CATEGORIE -> SUPPRESSION
		=================================
	*/
	if (isset($_['categoryDell']))
	{
		$query = $db->prepare('DELETE FROM `site_category` WHERE `id` = :id');
		$query->bindValue(':id', $_['categoryDell'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		$query = $db->query('ALTER TABLE `site_category` AUTO_INCREMENT = 1');
		
		$query = $db->prepare('SELECT `id`, `category` FROM `site_menu` WHERE `category` = :category');
		$query->bindValue(':category', $_['categoryDell'], PDO::PARAM_INT);
		$query->execute();
		$essai = $query->fetch();
		$query->CloseCursor();
		
		$query = $db->prepare('DELETE FROM `site_menu_filter` WHERE `menu` = :menu');
		$query->bindValue(':menu', $essai['id'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		$query = $db->query('ALTER TABLE `site_menu_filter` AUTO_INCREMENT = 1');
		
		$query = $db->prepare('DELETE FROM `site_menu` WHERE `category` = :category');
		$query->bindValue(':category', $_['categoryDell'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		$query = $db->query('ALTER TABLE `site_menu` AUTO_INCREMENT = 1');
		
		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		MENU -> AJOUT
		=================================
	*/
	if (isset($_['menuAddButton']))
	{
		// Vérification qu'il y a au moins une catégorie
		if (empty($_['menuCategory']))
		{
			$menuMessage = 'Veuillez ajouter une catégorie avant d\'ajouter un menu.';
			$i++;
		}
		// Vérification que tous les champs sont renseignés
		if ((empty($_['menuName']) || empty($_['menuTable']) || empty($_['menuIcon'])) && $i == 0)
		{
			$menuMessage = 'Veuillez renseigner le nom du menu, le nom de la table et le nom de l\'icone.';
			$i++;
		}
	}

	// Pas d'erreur, on ajoute le menu
	if (isset($_['menuAddButton']) && $i == 0)
	{
		$query = $db->prepare('INSERT INTO `site_menu` (`name`, `icon`, `category`, `table`, `type`, `position`) VALUES (:name, :icon, :category, :table, :type, :position)');
		$query->bindValue(':name', $_['menuName'], PDO::PARAM_STR);
		$query->bindValue(':icon', $_['menuIcon'], PDO::PARAM_STR);
		$query->bindValue(':category', $_['menuCategory'], PDO::PARAM_INT);
		$query->bindValue(':table', $_['menuTable'], PDO::PARAM_STR);
		$query->bindValue(':type', $_['menuType'], PDO::PARAM_STR);
		$query->bindValue(':position', '0', PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();

		if (!file_exists('./profils/'.$_['menuTable']))
		{
			$old = umask(0);
			mkdir('./profils/'.$_['menuTable'], 0777);
			umask($old);
		}

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		MENU -> MODIFICATION
		=================================
	*/
	if (isset($_['menuEditButton']))
	{
		$query = $db->prepare('UPDATE `site_menu` SET `name` = :name, `icon` = :icon, `category` = :category, `table` = :table, `type` = :type, `position` = :position WHERE `id` = :id');
		$query->bindValue(':name', $_['menuEditName'], PDO::PARAM_STR);
		$query->bindValue(':icon', $_['menuEditIcon'], PDO::PARAM_STR);
		$query->bindValue(':category', $_['menuEditCategory'], PDO::PARAM_INT);
		$query->bindValue(':table', $_['menuEditTable'], PDO::PARAM_STR);
		$query->bindValue(':type', $_['menuEditType'], PDO::PARAM_STR);
		$query->bindValue(':position', $_['menuEditPosition'], PDO::PARAM_INT);
		$query->bindValue(':id', $_['menuEditId'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		MENU -> SUPPRESSION
		=================================
	*/
	if (isset($_['menuDell']))
	{
		$query = $db->prepare('DELETE FROM `site_menu` WHERE `id` = :id');
		$query->bindValue(':id', $_['menuDell'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		$query = $db->query('ALTER TABLE `site_menu` AUTO_INCREMENT = 1');
		
		$query = $db->prepare('DELETE FROM `site_menu_filter` WHERE `menu` = :menu');
		$query->bindValue(':menu', $_['menuDell'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		$query = $db->query('ALTER TABLE `site_menu_filter` AUTO_INCREMENT = 1');

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		MENU -> FILTRES -> AJOUT
		=================================
	*/
	// Ajouter un filtre
	if (isset($_['menuFilterAddButton']))
	{
		// Vérification que le champ est renseigné
		if ((empty($_['menuFilterName']) || empty($_['menuFilterType']) || empty($_['menuFilterSort'])))
		{
			$menuFilterMessage = 'Veuillez renseigner le nom du filtre, le type du filtre et l\'ordre de tri du filtre.';
			$i++;
		}
	}

	// Pas d'erreur, on ajoute la catégorie
	if (isset($_['menuFilterAddButton']) && $i == 0)
	{
		$query = $db->prepare('INSERT INTO `site_menu_filter` (`name`, `type`, `sort`, `menu`, `position`) VALUES (:name, :type, :sort, :menu, :position)');
		$query->bindValue(':name', $_['menuFilterName'], PDO::PARAM_STR);
		$query->bindValue(':type', $_['menuFilterType'], PDO::PARAM_STR);
		$query->bindValue(':sort', $_['menuFilterSort'], PDO::PARAM_STR);
		$query->bindValue(':menu', $_['menuFilterMenu'], PDO::PARAM_STR);
		$query->bindValue(':position', '0', PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		MENU -> FILTRES -> MODIFICATION
		=================================
	*/
	if (isset($_['menuFilterEditButton']))
	{
		$query = $db->prepare('UPDATE `site_menu_filter` SET `name` = :name, `type` = :type, `sort` = :sort, `position` = :position WHERE `id` = :id');
		$query->bindValue(':name', $_['menuFilterEditName'], PDO::PARAM_STR);
		$query->bindValue(':type', $_['menuFilterEditType'], PDO::PARAM_STR);
		$query->bindValue(':sort', $_['menuFilterEditSort'], PDO::PARAM_STR);
		$query->bindValue(':position', $_['menuFilterEditPosition'], PDO::PARAM_INT);
		$query->bindValue(':id', $_['menuFilterEditId'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		MENU -> FILTRES -> SUPPRESSION
		=================================
	*/
	if (isset($_['menuFilterDell']))
	{
		$query = $db->prepare('DELETE FROM `site_menu_filter` WHERE `id` = :id');
		$query->bindValue(':id', $_['menuFilterDell'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		$query = $db->query('ALTER TABLE `site_menu_filter` AUTO_INCREMENT = 1');

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}
?>
<script>document.title += " / Paramètres"</script>
<div class="btn-group btn-group-justified">
	<div class="btn-group"><a href="?op=settings" class="btn btn-default <?php if ($tab == '1') echo 'active'; ?>">Options</a></div>
	<div class="btn-group"><a href="?op=settings&tab=2" class="btn btn-default <?php if ($tab == '2') echo 'active'; ?>">Membres</a></div>
	<div class="btn-group"><a href="?op=settings&tab=3" class="btn btn-default <?php if ($tab == '3') echo 'active'; ?>">Menus + Filtres</a></div>
</div>
<br />
<!-- OPTIONS -->
<?php if ($tab == '1') { ?>
	<div class="panel panel-default">
		<div class="panel-heading">Options</div>
		<form method="POST">
			<div class="panel-body">
				<h4>Général</h4>
				<div class="form-group">
					<label>Titre du site</label>
					<input type="text" class="form-control" name="optionsTitle" value="<?php echo $optionsTitle; ?>" />
				</div>
				<div class="form-group">
					<label>Ouvert au public</label>
					<div class="radio">
						<label><input type="radio" name="optionsOpen" value="0" <?php if ($config['open'] == '0') echo 'checked'; ?>> Non</label>
						<label><input type="radio" name="optionsOpen" value="1" <?php if ($config['open'] == '1') echo 'checked'; ?>> Oui</label>
					</div>
				</div>
				<br />
				<h4>Avatar</h4>
				<div class="form-group">
					<label>Largeur maximum</label>
					<input type="number" class="form-control" name="optionsAvatarMaxWidth" value="<?php echo $optionsAvatarMaxWidth; ?>" />
				</div>
				<div class="form-group">
					<label>Hauteur maximum</label>
					<input type="number" class="form-control" name="optionsAvatarMaxHeight" value="<?php echo $optionsAvatarMaxHeight; ?>" />
				</div>
				<div class="form-group">
					<label>Poids maximum</label>
					<input type="number" class="form-control" name="optionsAvatarMaxWeight" value="<?php echo $optionsAvatarMaxWeight; ?>" />
				</div>
				<br />
				<h4>Derniers ajouts</h4>
				<div class="form-group">
					<label>Nombre d'éléments</label>
					<input type="number" class="form-control" name="optionsLastaddMax" value="<?php echo $optionsLastaddMax; ?>" />
				</div>
			</div>
			<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right" name="optionsButton">Modifier</button></div>
		</form>
	</div>
<?php } ?>
<!-- MEMBRES -->
<?php if ($tab == '2') { ?>
	<div class="modal fade" id="modalMemberDell" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header"><h4 class="modal-title">Suppression d'un membre</h4></div>
				<form method="POST">
					<input type="hidden" name="memberDell" id="recipient">
					<div class="modal-body">Etes-vous sur de vouloir supprimer ce membre ?</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
						<button type="submit" class="btn btn-primary">Oui</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="panel panel-default table-responsive">
		<div class="panel-heading">Liste des membres</div>
		<table class="table table-bordered table-striped">
			<thead>
				<th>Nom d'utilisateur</th>
				<th>Email</th>
				<th>Rang</th>
				<th colspan="2">Action</th>
			</thead>
			<tbody>
				<?php while ($settings_members = $settings_members_query->fetch()) { ?>
					<tr>
						<td style="width:32%"><img src="./img/avatar/<?php echo $settings_members['avatar']; ?>" class="img-circle" alt="User Image" style="max-height:30px;" /> <a href="./?op=profile&userid=<?php echo $settings_members['id']; ?>"><?php echo $settings_members['username']; ?></a></td>
						<td style="width:32%"><?php echo $settings_members['mail']; ?></td>
						<td style="width:32%">
							<form method="POST">
								<input type="hidden" name="membersId" value="<?php echo $settings_members['id']; ?>" />
								<select class="form-control select2" name="membersRankSelect" onchange="this.form.submit()" style="width:100%;" <?php if ($settings_members['id'] == '1') echo 'disabled'; ?>>
									<option value="3" <?php if ($settings_members['rank'] == '3') echo 'selected'; ?>>Administrateur</option>
									<option value="2" <?php if ($settings_members['rank'] == '2') echo 'selected'; ?>>Membre</option>
									<option value="1" <?php if ($settings_members['rank'] == '1') echo 'selected'; ?>>Inviter</option>
								</select>
							</form>
						</td>
						<td class="text-center">
							<form method="POST">
								<input type="hidden" name="membersId" value="<?php echo $settings_members['id']; ?>" />
								<input type="hidden" name="membersAccess" value="<?php echo $settings_members['access']; ?>" />
								<button type="submit" class="btn btn-<?php if ($settings_members['access'] == '0') echo 'warning'; else echo 'primary'; ?> btn-xs" name="membersAccessButton" title="Accès" <?php if ($settings_members['id'] == '1') echo 'disabled'; ?>><i class="fa fa-<?php if($settings_members['access'] == '0') echo 'times'; else echo 'check'; ?>"></i></button>
							</form>
						</td>
						<td class="text-center"><form method="POST"><button type="button" class="btn btn-danger btn-xs" title="Supprimer le membre" data-toggle="modal" data-target="#modalMemberDell" data-whatever="<?php echo $settings_members['id']; ?>" <?php if ($settings_members['id'] == '1') echo 'disabled'; ?>><i class="fa fa-trash-o"></i></button></form></td>
					</tr>
				<?php } $settings_members_query->closeCursor(); ?>
			</tbody>
		</table>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Ajouter un membre</div>
		<form method="POST">
			<div class="panel-body">
				<?php if (isset($membersMessage)) echo '<div class="alert alert-danger">'.$membersMessage.'</div>'; ?>
				<div class="form-group <?php if (isset($membersMessage) || isset($membersMessageUsername)) echo 'has-error'; ?>">
					<label>Nom d'utilisateur</label>
					<input type="text" class="form-control" name="membersUsername" value="<?php echo $membersUsername; ?>" />
				</div>
				<?php if (isset($membersMessageUsername)) echo '<div class="alert alert-danger">'.$membersMessageUsername.'</div>'; ?>
				<div class="form-group <?php if (isset($membersMessage) || isset($membersMessageMail)) echo 'has-error'; ?>">
					<label>Email</label>
					<input type="email" class="form-control" name="membersMail" value="<?php echo $membersMail; ?>" />
				</div>
				<?php if (isset($membersMessageMail)) echo '<div class="alert alert-danger">'.$membersMessageMail.'</div>'; ?>
				<div class="form-group <?php if (isset($membersMessage) || isset($membersMessagePassword)) echo 'has-error'; ?>">
					<label>Mot de passe</label>
					<input type="password" class="form-control" name="membersPassword1" value="<?php echo $membersPassword1; ?>" autocomplete="off" />
				</div>
				<div class="form-group <?php if (isset($membersMessage) || isset($membersMessagePassword)) echo 'has-error'; ?>">
					<label>Retapez le mot de passe</label>
					<input type="password" class="form-control" name="membersPassword2" value="<?php echo $membersPassword2; ?>" autocomplete="off" />
				</div>
				<?php if (isset($membersMessagePassword)) echo '<div class="alert alert-danger">'.$membersMessagePassword.'</div>'; ?>
				<div class="form-group">
					<label>Rang</label>
					<div class="radio">
						<label><input type="radio" name="membersRank" value="3"> Administrateur</label>
						<label><input type="radio" name="membersRank" value="2" checked> Membre</label>
						<label><input type="radio" name="membersRank" value="1"> Inviter</label>
					</div>
				</div>
			</div>
			<div class="panel-footer clearfix">
				<button type="submit" class="btn btn-success pull-right" name="membersAddButton">Ajouter</button>
			</div>
		</form>
	</div>
<?php } ?>
<!-- MENUS + FILTRES -->
<?php if ($tab == '3') { ?>
	<div class="modal fade" id="modalCategoryDell" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header"><h4 class="modal-title">Suppression d'une catégorie</h4></div>
				<form method="POST" action="?op=settings&tab=<?php echo $tab; ?>">
					<input type="hidden" name="categoryDell" id="recipient">
					<div class="modal-body">Etes-vous sur de vouloir supprimer cette catégorie ? Cela supprimera tous les menus qu'elle contient.</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
						<button type="submit" class="btn btn-primary">Oui</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalMenuDell" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header"><h4 class="modal-title">Suppression d'un menu</h4></div>
				<form method="POST" action="?op=settings&tab=<?php echo $tab; ?>">
					<input type="hidden" name="menuDell" id="recipient">
					<div class="modal-body">Etes-vous sur de vouloir supprimer ce menu ?</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
						<button type="submit" class="btn btn-primary">Oui</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalMenuFilterDell" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header"><h4 class="modal-title">Suppression d'un filtre</h4></div>
				<form method="POST">
					<input type="hidden" name="menuFilterDell" id="recipient">
					<div class="modal-body">Etes-vous sur de vouloir supprimer ce filtre ?</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
						<button type="submit" class="btn btn-primary">Oui</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">Liste des menus</div>
				<table class="table table-bordered">
					<?php while ($settings_category = $settings_category_query->fetch()) { ?>
						<tr>
							<td style="width:96%;background-color:#f9f9f9;" colspan="3"><?php echo $settings_category['name']; ?></td>
							<td class="text-center" style="background-color:#f9f9f9;"><a href="?op=settings&tab=3&type=edit_category&id=<?php echo $settings_category['id']; ?>" class="btn btn-info btn-xs" name="categoryEditButton" title="Modifier la catégorie"><i class="fa fa-pencil"></i></a></td>
							<td class="text-center" style="background-color:#f9f9f9;"><form method="POST"><button type="button" class="btn btn-danger btn-xs" title="Supprimer la catégorie" data-toggle="modal" data-target="#modalCategoryDell" data-whatever="<?php echo $settings_category['id']; ?>"><i class="fa fa-trash-o"></i></button></form></td>
						</tr>
						<?php
							$settings_menu_query = $db->prepare('SELECT `id`, `name`, `icon`, `category`, `table`, `type`, `position` FROM `site_menu` WHERE `category` = :category ORDER BY `position`');
							$settings_menu_query->bindValue(':category', $settings_category['id'], PDO::PARAM_STR);
							$settings_menu_query->execute();
						?>
						<?php while ($settings_menu = $settings_menu_query->fetch()) { ?>
							<tr>
								<td><i class="fa fa-<?php echo $settings_menu['icon']; ?>"></i></td>
								<td style="width:89%;"><?php echo $settings_menu['name']; ?></td>
								<td class="text-center"><a href="?op=settings&tab=3&type=edit_menu&id=<?php echo $settings_menu['id']; ?>" class="btn btn-info btn-xs" name="menuEditButton" title="Modifier le menu"><i class="fa fa-pencil"></i></a></td>
								<td class="text-center"><a href="?op=settings&tab=3&type=edit_menu_filter&id=<?php echo $settings_menu['id']; ?>" class="btn btn-primary btn-xs" name="menuEditButton" title="Ajouter et modifier les filtres"><i class="fa fa-align-justify"></i></a></td>
								<td class="text-center"><form method="POST"><button type="button" class="btn btn-danger btn-xs" title="Supprimer le menu" data-toggle="modal" data-target="#modalMenuDell" data-whatever="<?php echo $settings_menu['id']; ?>"><i class="fa fa-trash-o"></i></button></form></td>
							</tr>
						<?php } $settings_menu_query->closeCursor(); ?>
					<?php } $settings_category_query->closeCursor(); ?>
					<tr><td colspan="5"><a href="?op=settings&tab=3&type=add_category">+ Ajouter une catégorie</a></td></tr>
					<tr><td colspan="5"><a href="?op=settings&tab=3&type=add_menu">+ Ajouter un menu</a></td></tr>
				</table>
			</div>
		</div>
		<?php if ($type == 'add_category') { ?>
			<div class="col-xs-12 col-sm-12 col-md-8">
				<div class="panel panel-default">
					<div class="panel-heading">Ajouter une catégorie</div>
					<form method="POST">
						<div class="panel-body">
							<?php if (isset($categoryMessage)) echo '<div class="alert alert-danger">'.$categoryMessage.'</div>'; ?>
							<div class="form-group <?php if (isset($categoryMessage)) echo 'has-error'; ?>">
								<label>Nom de la catégorie</label>
								<input type="text" class="form-control" name="categoryName" value="<?php echo $categoryName; ?>" />
							</div>
						</div>
						<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right" name="categoryAddButton">Ajouter</button></div>
					</form>
				</div>
			</div>
		<?php } ?>
		<?php if ($type == 'edit_category') { ?>
			<div class="col-xs-12 col-sm-12 col-md-8">
				<?php
					$query = $db->prepare('SELECT `id`, `name` FROM `site_category` WHERE `id` = :id');
					$query->bindValue(':id', $id, PDO::PARAM_STR);
					$query->execute();
					$settings_category = $query->fetch();
					$query->CloseCursor();
				?>
				<div class="panel panel-default">
					<div class="panel-heading">Modifier une catégorie</div>
					<form method="POST">
						<input type="hidden" name="categoryEditId" value="<?php echo $id; ?>" />
						<div class="panel-body">
							<div class="form-group">
								<label>Nom de la catégorie</label>
								<input type="text" class="form-control" name="categoryEditName" value="<?php echo $settings_category['name']; ?>" />
							</div>
						</div>
						<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right" name="categoryEditButton">Modifier</button></div>
					</form>
				</div>
			</div>
		<?php } ?>
		<?php if ($type == 'add_menu') { ?>
			<div class="col-xs-12 col-sm-12 col-md-8">
				<div class="panel panel-default">
					<div class="panel-heading">Ajouter un menu</div>
					<form method="POST">
						<div class="panel-body">
							<?php if (isset($menuMessage)) echo '<div class="alert alert-danger">'.$menuMessage.'</div>'; ?>
							<div class="form-group <?php if (isset($menuMessage)) echo 'has-error'; ?>">
								<label>Nom du menu</label>
								<input type="text" class="form-control" name="menuName" value="<?php echo $menuName; ?>" />
							</div>
							<div class="form-group <?php if (isset($menuMessage)) echo 'has-error'; ?>">
								<label>Nom de la table</label>
								<input type="text" class="form-control" name="menuTable" value="<?php echo $menuTable; ?>" />
							</div>
							<div class="form-group <?php if (isset($menuMessage)) echo 'has-error'; ?>">
								<label>Nom de l'icône</label>
								<div class="input-group">
									<input type="text" class="form-control" name="menuIcon" value="<?php echo $menuIcon; ?>" id="menuIcon" />
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
								<label>Catégorie</label>
								<select class="form-control select2" name="menuCategory" style="width:100%;">
									<?php
										$menu_category_query = $db->prepare('SELECT `id`, `name` FROM `site_category`');
										$menu_category_query->execute();
									?>
									<?php while ($menu_category = $menu_category_query->fetch()) { ?>
										<option value="<?php echo $menu_category['id']; ?>"><?php echo $menu_category['name']; ?></option>
									<?php } $menu_category_query->closeCursor(); ?>
								</select>
							</div>
							<div class="form-group">
								<label>Type</label>
								<select class="form-control select2" name="menuType" style="width:100%;">
									<option value="autre">Autre</option>
									<option value="jeuxvideo">Jeux Vidéo</option>
									<option value="livre">Livre</option>
									<option value="musique">Musique</option>
									<option value="video">Vidéo</option>
								</select>
							</div>
						</div>
						<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right" name="menuAddButton">Ajouter</button></div>
					</form>
				</div>
			</div>
		<?php } ?>
		<?php if ($type == 'edit_menu') { ?>
			<div class="col-xs-12 col-sm-12 col-md-8">
				<?php
					$query = $db->prepare('SELECT `id`, `name`, `icon`, `category`, `table`, `type`, `position` FROM `site_menu` WHERE `id` = :id');
					$query->bindValue(':id', $id, PDO::PARAM_STR);
					$query->execute();
					$settings_menu = $query->fetch();
					$query->CloseCursor();
				?>
				<div class="panel panel-default">
					<div class="panel-heading">Modifier un menu</div>
					<form method="POST">
						<input type="hidden" name="menuEditId" value="<?php echo $id; ?>" />
						<div class="panel-body">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-4">
									<div class="form-group">
										<label>Position du menu</label>
										<input type="text" class="form-control" name="menuEditPosition" value="<?php echo $settings_menu['position']; ?>" />
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-8">
									<div class="form-group">
										<label>Nom du menu</label>
										<input type="text" class="form-control" name="menuEditName" value="<?php echo $settings_menu['name']; ?>" />
									</div>
								</div>
							</div>
							<div class="form-group">
								<label>Nom de la table</label>
								<input type="text" class="form-control" name="menuEditTable" value="<?php echo $settings_menu['table']; ?>" />
							</div>
							<div class="form-group">
								<label>Nom de l'icône</label>
								<div class="input-group">
									<input type="text" class="form-control" name="menuEditIcon" value="<?php echo $settings_menu['icon']; ?>" id="menuIcon" />
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
								<select class="form-control select2" name="menuEditCategory" style="width:100%;">
									<?php
										$menu_category_query = $db->prepare('SELECT `id`, `name` FROM `site_category`');
										$menu_category_query->execute();
									?>
									<?php
										while ($menu_category = $menu_category_query->fetch())
										{
											if ($menu_category['id'] == $settings_menu['category']) $selected = 'selected'; else echo $selected = '';
											echo '<option value="'.$menu_category['id'].'" '.$selected.'>'.$menu_category['name'].'</option>';
										}
										$menu_category_query->closeCursor();
									?>
								</select>
							</div>
							<div class="form-group">
								<label>Type de la table</label>
								<select class="form-control select2" name="menuEditType" style="width:100%;">
									<option value="autre" <?php if ($settings_menu['type'] == 'autre') echo 'selected'; ?>>Autre</option>
									<option value="jeuxvideo" <?php if ($settings_menu['type'] == 'jeuxvideo') echo 'selected'; ?>>Jeux Vidéo</option>
									<option value="livre" <?php if ($settings_menu['type'] == 'livre') echo 'selected'; ?>>Livre</option>
									<option value="musique" <?php if ($settings_menu['type'] == 'musique') echo 'selected'; ?>>Musique</option>
									<option value="video" <?php if ($settings_menu['type'] == 'video') echo 'selected'; ?>>Vidéo</option>
								</select>
							</div>
						</div>
						<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right" name="menuEditButton">Modifier</button></div>
					</form>
				</div>
			</div>
		<?php } ?>
		<?php if ($type == 'edit_menu_filter') { ?>
			<div class="col-xs-12 col-sm-12 col-md-8">
				<div class="panel panel-default">
					<div class="panel-heading">Ajouter un filtre</div>
					<form method="POST">
						<input type="hidden" name="menuFilterMenu" value="<?php echo $id; ?>" />
						<div class="panel-body">
							<?php if (isset($menuFilterMessage)) echo '<div class="alert alert-danger">'.$menuFilterMessage.'</div>'; ?>
							<div class="form-group <?php if (isset($menuFilterMessage)) echo 'has-error'; ?>">
								<label>Nom du filtre</label>
								<input type="text" class="form-control" name="menuFilterName" value="<?php echo $menuFilterName; ?>" />
							</div>
							<div class="form-group <?php if (isset($menuFilterMessage)) echo 'has-error'; ?>">
								<label>Type du filtre</label>
								<select class="form-control select2" name="menuFilterType" style="width:100%;">
									<option value="acteurs">Acteurs</option>
									<option value="annee">Année</option>
									<option value="audio">Audio</option>
									<option value="commentaires">Commentaires</option>
									<option value="duree">Durée</option>
									<option value="edition">Edition</option>
									<option value="filmvu">Film Vu</option>
									<option value="genre">Genre</option>
									<option value="note">Note</option>
									<option value="pays">Pays</option>
									<option value="realisateurs">Réalisateurs</option>
									<option value="reference">Référence</option>
									<option value="soustitres">Sous-titres</option>
									<option value="support">Support</option>
									<option value="zone">Zone</option>
								</select>
							</div>
							<div class="form-group <?php if (isset($menuFilterMessage)) echo 'has-error'; ?>">
								<label>Ordre de tri du filtre</label>
								<div class="radio">
									<label><input type="radio" name="menuFilterSort" value="sort"> <i class="fa fa-long-arrow-down"></i></label>
									<label><input type="radio" name="menuFilterSort" value="rsort"> <i class="fa fa-long-arrow-up"></i></label>
								</div>
							</div>
						</div>
						<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right" name="menuFilterAddButton">Ajouter</button></div>
					</form>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-4">
				<div class="panel panel-default table-responsive">
					<div class="panel-heading">Liste des filtres</div>
					<?php
						$settings_menu_filter_query = $db->prepare('SELECT `id`, `name`, `type`, `sort`, `menu`, `position` FROM `site_menu_filter` WHERE `menu` = :menu ORDER BY `position`');
						$settings_menu_filter_query->bindValue(':menu', $id, PDO::PARAM_INT);
						$settings_menu_filter_query->execute();
					?>
					<table class="table table-bordered table-striped">
						<thead>
							<th>Position</th>
							<th>Nom</th>
							<th>Type</th>
							<th>Ordre de tri</th>
							<th colspan="2">Action</th>
						</thead>
						<tbody>
							<?php while ($settings_menu_filter = $settings_menu_filter_query->fetch()) { ?>
								<tr>
									<form method="POST">
										<input type="hidden" name="menuFilterEditId" value="<?php echo $settings_menu_filter['id']; ?>" />
										<td style="width:18%;"><input type="text" class="form-control" name="menuFilterEditPosition" value="<?php echo $settings_menu_filter['position']; ?>" /></td>
										<td style="width:30%;"><input type="text" class="form-control" name="menuFilterEditName" value="<?php echo $settings_menu_filter['name']; ?>" /></td>
										<td style="width:30%;">
											<select class="form-control select2" name="menuFilterEditType" style="width:100%;">
												<option value="acteurs" <?php if ($settings_menu_filter['type'] == 'acteurs') echo 'selected'; ?>>Acteurs</option>
												<option value="annee" <?php if ($settings_menu_filter['type'] == 'annee') echo 'selected'; ?>>Année</option>
												<option value="audio" <?php if ($settings_menu_filter['type'] == 'audio') echo 'selected'; ?>>Audio</option>
												<option value="commentaires" <?php if ($settings_menu_filter['type'] == 'commentaires') echo 'selected'; ?>>Commentaires</option>
												<option value="duree" <?php if ($settings_menu_filter['type'] == 'duree') echo 'selected'; ?>>Durée</option>
												<option value="edition" <?php if ($settings_menu_filter['type'] == 'edition') echo 'selected'; ?>>Edition</option>
												<option value="filmvu" <?php if ($settings_menu_filter['type'] == 'filmvu') echo 'selected'; ?>>Film Vu</option>
												<option value="genre" <?php if ($settings_menu_filter['type'] == 'genre') echo 'selected'; ?>>Genre</option>
												<option value="note" <?php if ($settings_menu_filter['type'] == 'note') echo 'selected'; ?>>Note</option>
												<option value="pays" <?php if ($settings_menu_filter['type'] == 'pays') echo 'selected'; ?>>Pays</option>
												<option value="realisateurs" <?php if ($settings_menu_filter['type'] == 'realisateurs') echo 'selected'; ?>>Réalisateurs</option>
												<option value="reference" <?php if ($settings_menu_filter['type'] == 'reference') echo 'selected'; ?>>Référence</option>
												<option value="soustitres" <?php if ($settings_menu_filter['type'] == 'soustitres') echo 'selected'; ?>>Sous-titres</option>
												<option value="support" <?php if ($settings_menu_filter['type'] == 'support') echo 'selected'; ?>>Support</option>
												<option value="zone" <?php if ($settings_menu_filter['type'] == 'zone') echo 'selected'; ?>>Zone</option>
											</select>
										</td>
										<td style="width:18%;">
											<div class="radio">
												<label><input type="radio" name="menuFilterEditSort" value="sort" <?php if($settings_menu_filter['sort'] == 'sort') echo 'checked'; ?>> <i class="fa fa-long-arrow-down" title="Croissant"></i></label>
												<label><input type="radio" name="menuFilterEditSort" value="rsort" <?php if($settings_menu_filter['sort'] == 'rsort') echo 'checked'; ?>> <i class="fa fa-long-arrow-up" title="Déroissant"></i></label>
											</div>
										</td>
										<td class="text-center"><button type="submit" class="btn btn-success btn-xs" name="menuFilterEditButton" title="Modifier le filtre"><i class="fa fa-check"></i></button></td>
										<td class="text-center"><button type="button" class="btn btn-danger btn-xs" title="Supprimer le filtre" data-toggle="modal" data-target="#modalMenuFilterDell" data-whatever="<?php echo $settings_menu_filter['id']; ?>"><i class="fa fa-trash-o"></i></button></td>
									</form>
								</tr>
							<?php } $settings_menu_filter_query->closeCursor(); ?>
						</tbody>
					</table>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } ?>