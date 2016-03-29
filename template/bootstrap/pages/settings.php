<?php
	if(!isset($_SESSION['username']) || $user['rank'] != '3')
	{
		header('location: ./');
		exit();
	}
	
	if($tab > '3') $tab = '1';
	
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach($_ as $key=>&$val)
	{
		Functions::secure($val);
	}

	// Valeurs par défaut, remplacées si une autre valeur est saisie
	foreach (array('title', 'open', 'avatarMaxWidth', 'avatarMaxHeight', 'avatarMaxWeight', 'lastaddMax', 'username', 'mail', 'password1', 'password2', 'categoryName', 'menuName', 'menuTable', 'menuIcon') as $var)
	{
		if (!empty($_[$var]))
		{
			$$var = $_[$var];
		} else {
			$$var = '';
		}
	}
	
	$lib_errors = 'Erreurs';
	$lib_success = 'Succès';
	
	/*
		=================================
		OPTIONS
		=================================
	*/
	if (empty($_['title']))
	{
		$title = $config['title'];
	}
	if (empty($_['avatarMaxWidth']))
	{
		$avatarMaxWidth = $config['avatarMaxWidth'];
	}
	if (empty($_['avatarMaxHeight']))
	{
		$avatarMaxHeight = $config['avatarMaxHeight'];
	}
	if (empty($_['avatarMaxWeight']))
	{
		$avatarMaxWeight = $config['avatarMaxWeight'];
	}
	if (empty($_['lastaddMax']))
	{
		$lastaddMax = $config['lastaddMax'];
	}
	
	if(isset($_['optionsButton']))
	{
		$query = $db->prepare('UPDATE site_configuration SET `value` = :value WHERE `key` = "open"');
		$query->bindValue(':value', $_['open'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();
		
		$query = $db->prepare('UPDATE site_configuration SET `value` = :value WHERE `key` = "title"');
		$query->bindValue(':value', $_['title'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();
		
		$query = $db->prepare('UPDATE site_configuration SET `value` = :value WHERE `key` = "avatarMaxWidth"');
		$query->bindValue(':value', $_['avatarMaxWidth'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();
		
		$query = $db->prepare('UPDATE site_configuration SET `value` = :value WHERE `key` = "avatarMaxHeight"');
		$query->bindValue(':value', $_['avatarMaxHeight'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();
		
		$query = $db->prepare('UPDATE site_configuration SET `value` = :value WHERE `key` = "avatarMaxWeight"');
		$query->bindValue(':value', $_['avatarMaxWeight'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();
		
		$query = $db->prepare('UPDATE site_configuration SET `value` = :value WHERE `key` = "lastaddMax"');
		$query->bindValue(':value', $_['lastaddMax'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();
		
		refresh($_SERVER['REQUEST_URI']);
	}
	
	/*
		=================================
		MEMBRES
		=================================
	*/
	// Modifier le rang d'un membre
	if(isset($_['membersRankSelect']))
	{
		$query = $db->prepare('UPDATE site_user SET rank = :rank WHERE id = :id');
		$query->bindValue(':rank', $_['membersRankSelect'], PDO::PARAM_STR);
		$query->bindValue(':id', $_['membersId'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();
		
		refresh($_SERVER['REQUEST_URI']);
	}
	
	// Modifier l'accès d'un membre
	if(isset($_['membersAccessButton']))
	{
		if($_['membersAccess'] == '0')
		{
			$query = $db->prepare('UPDATE site_user SET access = :access WHERE id = :id');
			$query->bindValue(':access', '1', PDO::PARAM_STR);
			$query->bindValue(':id', $_['membersId'], PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();
		}
		else
		{
			$query = $db->prepare('UPDATE site_user SET access = :access WHERE id = :id');
			$query->bindValue(':access', '0', PDO::PARAM_STR);
			$query->bindValue(':id', $_['membersId'], PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();
		}
		
		refresh($_SERVER['REQUEST_URI']);
	}
	
	// Supprimer un membre
	if (isset($_['membersDell']))
	{
		$query = $db->prepare('DELETE FROM site_user WHERE `id` = :id');
		$query->bindValue(':id', $_['membersDell'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();
		
		$query = $db->query('ALTER TABLE `site_user` AUTO_INCREMENT = 1');

		refresh($_SERVER['REQUEST_URI']);
	}
	
	// Ajouter un membre
	if(isset($_['membersAddButton']))
	{
		if (empty($_['username']) || empty($_['mail']) || empty($_['password1']))
		{
			$membersMessage[$lib_errors][] = 'Vous devez renseigner le nom d\'utilisateur, l\'email et le mot de passe.';
		}

		// Vérification de la disponibilité du nom de l'utilisateur
		$query = $db->prepare('SELECT COUNT(id) FROM site_user WHERE `username` = :username');
		$query->bindValue(':username', $_['username'], PDO::PARAM_STR);
		$query->execute();
		$user_free = $query->fetchColumn();
		$query->CloseCursor();
		if ($user_free > 0 && empty($membersMessage[$lib_errors]))
		{
			$membersMessage[$lib_errors][] = 'Ce nom d\'utilisateur est déja utilisé par un autre membre.';
		}

		// Vérification de la disponibilité de l'email
		$query = $db->prepare('SELECT COUNT(id) FROM site_user WHERE `mail` = :mail');
		$query->bindValue(':mail', $_['mail'], PDO::PARAM_STR);
		$query->execute();
		$mail_free = $query->fetchColumn();
		$query->CloseCursor();
		if ($mail_free > 0 && empty($membersMessage[$lib_errors]))
		{
			$membersMessage[$lib_errors][] = 'Cette adresse email est déja utilisée par un autre membre.';
		}
		
		// Vérification des 2 mots de passe
		if ($_['password1'] != $_['password2'] && empty($membersMessage[$lib_errors]))
		{
			$membersMessage[$lib_errors][] = 'Le mot de passe et le mot de passe de confirmation ne sont pas identiques.';
		}
	}
	
	if(isset($_['membersAddButton']) && empty($membersMessage[$lib_errors]))
	{
		$query = $db->prepare('INSERT INTO site_user (`username`, `password`, `mail`, `date_registration`, `rank`) VALUES (:username, :password, :mail, :date_registration, :rank)');
		$query->bindValue(':username', $_['username'], PDO::PARAM_STR);
		$query->bindValue(':password', md5($_['password1']), PDO::PARAM_STR);
		$query->bindValue(':mail', $_['mail'], PDO::PARAM_STR);
		$query->bindValue(':date_registration', date('Y-m-d'), PDO::PARAM_INT);
		$query->bindValue(':rank', $_['rank'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();

		refresh($_SERVER['REQUEST_URI']);
	}
	
	/*
		=================================
		MENU
		=================================
	*/
	// Supprimer une catégorie
	if (isset($_['categoryDell']))
	{
		$query = $db->prepare('DELETE FROM site_category WHERE `id` = :id');
		$query->bindValue(':id', $_['categoryDell'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();
		
		$query = $db->query('ALTER TABLE site_category AUTO_INCREMENT = 1');
		
		$query = $db->prepare('DELETE FROM `site_menu` WHERE `category` = :category');
		$query->bindValue(':category', $_['categoryDell'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();
		
		$query = $db->query('ALTER TABLE site_menu AUTO_INCREMENT = 1');
		
		refresh($_SERVER['REQUEST_URI']);
	}
	
	// Supprimer un menu
	if (isset($_['menuDell']))
	{
		$query = $db->prepare('DELETE FROM site_menu WHERE `id` = :id');
		$query->bindValue(':id', $_['menuDell'], PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();
		
		$query = $db->query('ALTER TABLE `site_menu` AUTO_INCREMENT = 1');

		refresh($_SERVER['REQUEST_URI']);
	}
	
	// Ajouter une catégorie
	if(isset($_['categoryButton']))
	{
		if (empty($_['categoryName']))
		{
			$categoryMessage[$lib_errors][] = 'Vous devez renseigner le nom de la catégorie.';
		}
	}
	
	if(isset($_['categoryButton']) && empty($categoryMessage[$lib_errors]))
	{
		$query = $db->prepare('INSERT INTO site_category (`name`) VALUES (:name)');
		$query->bindValue(':name', $_['categoryName'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();
		
		refresh($_SERVER['REQUEST_URI']);
	}
	
	// Ajouter un menu
	if(isset($_['menuButton']))
	{
		if (empty($_['menuCategory']))
		{
			$menuMessage[$lib_errors][] = 'Vous devez ajouter une catégorie avant d\'ajouter un menu.';
		}
		elseif (empty($_['menuName']) || empty($_['menuTable']) || empty($_['menuIcon']))
		{
			$menuMessage[$lib_errors][] = 'Vous devez renseigner le nom du menu, le nom de la table et le nom de l\'icone.';
		}
	}
	
	if(isset($_['menuButton']) && empty($menuMessage[$lib_errors]))
	{
		$query = $db->prepare('INSERT INTO site_menu (`name`, `icon`, `category`, `table`, `type`) VALUES (:name, :icon, :category, :table, :type)');
		$query->bindValue(':name', $_['menuName'], PDO::PARAM_STR);
		$query->bindValue(':icon', $_['menuIcon'], PDO::PARAM_STR);
		$query->bindValue(':category', $_['menuCategory'], PDO::PARAM_INT);
		$query->bindValue(':table', $_['menuTable'], PDO::PARAM_STR);
		$query->bindValue(':type', $_['menuType'], PDO::PARAM_STR);
		$query->execute();
		$query->CloseCursor();
		
		if(!file_exists('profils/'.$_['menuTable']))
		{
			$old = umask(0);
			mkdir('profils/'.$_['menuTable'], 0777);
			umask($old);
		}
		
		refresh($_SERVER['REQUEST_URI']);
	}
?>
<script>document.title += ' - Paramètres'</script>		
<div class="btn-group btn-group-justified">
	<div class="btn-group"><a href="?op=settings" class="btn btn-default <?php if($tab == '1') echo 'active'; ?>">Options</a></div>
	<div class="btn-group"><a href="?op=settings&tab=2" class="btn btn-default <?php if($tab == '2') echo 'active'; ?>">Membres</a></div>
	<div class="btn-group"><a href="?op=settings&tab=3" class="btn btn-default <?php if($tab == '3') echo 'active'; ?>">Menu</a></div>
</div>
<br/>
<!-- OPTIONS -->
<?php if($tab == '1') { ?>
	<div class="panel panel-default">
		<div class="panel-heading">Options</div>
		<form method="POST">
			<div class="panel-body">
				<h4>Général</h4>
				<div class="form-group">
					<label>Titre du site</label>
					<input type="text" class="form-control" name="title" value="<?php echo $title; ?>" />
				</div>
				<div class="form-group">
					<label>Ouvert au public</label>
					<div class="radio">
						<label><input type="radio" name="open" value="0" <?php if($config['open'] == '0') echo 'checked'; ?>> Non</label>
						<label><input type="radio" name="open" value="1" <?php if($config['open'] == '1') echo 'checked'; ?>> Oui</label>
					</div>
				</div>
				<br/>
				<h4>Avatar</h4>
				<div class="form-group">
					<label>Largeur maximum</label>
					<input type="number" class="form-control" name="avatarMaxWidth" value="<?php echo $avatarMaxWidth; ?>" />
				</div>
				<div class="form-group">
					<label>Hauteur maximum</label>
					<input type="number" class="form-control" name="avatarMaxHeight" value="<?php echo $avatarMaxHeight; ?>" />
				</div>
				<div class="form-group">
					<label>Poids maximum</label>
					<input type="number" class="form-control" name="avatarMaxWeight" value="<?php echo $avatarMaxWeight; ?>" />
				</div>
				<br/>
				<h4>Derniers ajouts</h4>
				<div class="form-group">
					<label>Nombre d'éléments</label>
					<input type="number" class="form-control" name="lastaddMax" value="<?php echo $lastaddMax; ?>" />
				</div>
			</div>
			<div class="panel-footer clearfix">
				<button type="submit" name="optionsButton" class="btn btn-success pull-right">Modifier</button>
			</div>
		</form>
	</div>
<?php } ?>
<!-- MEMBERS -->
<?php if($tab == '2') { ?>
	<div class="modal fade" id="modalMembersDell" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Suppression d'un membre</h4>
				</div>
				<form method="POST">
					<input type="hidden" name="membersDell" id="recipient-name">
					<div class="modal-body">
						Etes vous sur de vouloir supprimer ce membre ?
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
						<button type="submit" class="btn btn-primary">Oui</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Liste des membres</div>
		<table class="table table-bordered table-striped">
			<thead>
				<th width="32%">Nom d'utilisateur</th>
				<th width="32%">Email</th>
				<th width="32%">Rang</th>
				<th colspan="2">Action</th>
			</thead>
			<tbody>
				<?php while($settings_members = $settings_members_query->fetch()) { ?>
					<tr>
						<td><img src="img/avatar/<?php echo $settings_members['avatar']; ?>" class="img-circle" alt="User Image" style="max-height:30px;" /> <?php echo $settings_members['username']; ?></td>
						<td><?php echo $settings_members['mail']; ?></td>
						<td>
							<form method="POST">
								<input type="hidden" name="membersId" value="<?php echo $settings_members['id']; ?>" />
								<select class="form-control" name="membersRankSelect" onchange="this.form.submit()" <?php if($settings_members['id'] == '1') echo 'disabled'; ?>>
									<option value="3" <?php if ($settings_members['rank'] == '3') echo 'selected'; ?>>Administrateur</option>
									<option value="2" <?php if ($settings_members['rank'] == '2') echo 'selected'; ?>>Membre</option>
									<option value="1" <?php if ($settings_members['rank'] == '1') echo 'selected'; ?>>Inviter</option>
								</select>
							</form>
						</td>
						<td>
							<form method="POST">
								<input type="hidden" name="membersId" value="<?php echo $settings_members['id']; ?>" />
								<input type="hidden" name="membersAccess" value="<?php echo $settings_members['access']; ?>" />
								<button type="submit" name="membersAccessButton" class="btn btn-<?php if($settings_members['access'] == '0') echo 'warning'; else echo 'primary'; ?> btn-xs" title="Accès"><i class="fa fa-<?php if($settings_members['access'] == '0') echo 'times'; else echo 'check'; ?>"></i></button>
							</form>
						</td>
						<td>
							<form method="POST">
								<button type="button" class="btn btn-danger btn-xs" title="Supprimer" data-toggle="modal" data-target="#modalMembersDell" data-whatever="<?php echo $settings_members['id']; ?>"><i class="fa fa-trash-o"></i></button>
							</form>
						</td>
					</tr>
				<?php } $settings_members_query->closeCursor(); ?>
			</tbody>
		</table>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Ajouter un membre</div>
		<form method="POST">
			<div class="panel-body">
				<?php
					if(isset($membersMessage[$lib_errors]))
					{
						foreach($membersMessage as $type=>$messages)
						{
							$class = 'alert ';
							$class .= $lib_errors==$type?'alert-danger':'alert-success';
							foreach ($messages as $message)
							{
								echo '<div class="'.$class.'">'.$message.'</div>';
							}
						}
					}
				?>
				<div class="form-group">
					<label>Nom d'utilisateur</label>
					<input type="text" class="form-control" name="username" value="<?php echo $username; ?>" />
				</div>
				<div class="form-group">
					<label>Email</label>
					<input type="email" class="form-control" name="mail" value="<?php echo $mail; ?>" />
				</div>
				<div class="form-group">
					<label>Mot de passe</label>
					<input type="password" class="form-control" name="password1" value="<?php echo $password1; ?>" />
				</div>
				<div class="form-group">
					<label>Retapez le mot de passe</label>
					<input type="password" class="form-control" name="password2" value="<?php echo $password2; ?>" />
				</div>
				<div class="form-group">
					<label>Rang</label>
					<div class="radio">
						<label><input type="radio" name="rank" value="3"> Administrateur</label>
						<label><input type="radio" name="rank" value="2" checked> Membre</label>
						<label><input type="radio" name="rank" value="1"> Inviter</label>
					</div>
				</div>
			</div>
			<div class="panel-footer clearfix">
				<button type="submit" name="membersAddButton" class="btn btn-success pull-right">Ajouter</button>
			</div>
		</form>
	</div>
<?php } ?>
<!-- MENU -->
<?php if($tab == '3') { ?>
	<div class="modal fade" id="modalCategoryDell" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Suppression d'une catégorie</h4>
				</div>
				<form method="POST">
					<div class="modal-body">
						Etes vous sur de vouloir supprimer cette catégorie ? Cela supprimera tous les menus qu'il contient.
						<input type="hidden" name="categoryDell" id="recipient-name">
					</div>
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
				<div class="modal-header">
					<h4 class="modal-title">Suppression d'un menu</h4>
				</div>
				<form method="POST">
					<div class="modal-body">
						Etes vous sur de vouloir supprimer ce menu ?
						<input type="hidden" name="menuDell" id="recipient-name">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
						<button type="submit" class="btn btn-primary">Oui</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Liste des menus</div>
		<?php while($settings_category = $settings_category_query->fetch()) { ?>
			<table class="table table-bordered">
				<tr>
					<td style="width:96%; background-color: #f9f9f9;" colspan="3"><?php echo strtoupper($settings_category['name']); ?></td>
					<td style="background-color: #f9f9f9;">
						<form method="POST">
							<button type="button" class="btn btn-primary btn-xs" title="Modifier"><i class="fa fa-check"></i></button>
						</form>
					</td>
					<td style="background-color: #f9f9f9;">
						<form method="POST">
							<button type="button" class="btn btn-danger btn-xs" title="Supprimer" data-toggle="modal" data-target="#modalCategoryDell" data-whatever="<?php echo $settings_category['id']; ?>"><i class="fa fa-trash-o"></i></button>
						</form>
					</td>
				</tr>
				<?php
					$settings_menu_query = $db->prepare('SELECT `id`, `name`, `icon`, `category`, `table`, `type` FROM site_menu WHERE `category` = "'.$settings_category['id'].'" ORDER BY `name`');
					$settings_menu_query->execute();
				?>
				<?php while($settings_menu = $settings_menu_query->fetch()) { ?>
					<tr>
						<td width="32%"><i class="fa fa-<?php echo $settings_menu['icon']; ?>"></i> <?php echo ucfirst($settings_menu['name']); ?></td>
						<td width="32%"><?php echo $settings_menu['table']; ?></td>
						<td width="32%"><?php echo $settings_menu['type']; ?></td>
						<td>
							<form method="POST">
								<button type="button" class="btn btn-primary btn-xs" title="Modifier"><i class="fa fa-check"></i></button>
							</form>
						</td>
						<td>
							<form method="POST">
								<button type="button" class="btn btn-danger btn-xs" title="Supprimer" data-toggle="modal" data-target="#modalMenuDell" data-whatever="<?php echo $settings_menu['id']; ?>"><i class="fa fa-trash-o"></i></button>
							</form>
						</td>
					</tr>
				<?php } $settings_menu_query->closeCursor(); ?>
			</table>
		<?php } $settings_category_query->closeCursor(); ?>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">Ajouter une catégorie</div>
				<form method="POST">
					<div class="panel-body">
						<?php
							if(isset($categoryMessage[$lib_errors]))
							{
								foreach($categoryMessage as $type=>$messages)
								{
									$class = 'alert ';
									$class .= $lib_errors==$type?'alert-danger':'alert-success';
									foreach ($messages as $message)
									{
										echo '<div class="'.$class.'">'.$message.'</div>';
									}
								}
							}
						?>
						<div class="form-group">
							<label>Nom de la catégorie</label>
							<input type="text" class="form-control" name="categoryName" value="<?php echo $categoryName; ?>" />
						</div>
					</div>
					<div class="panel-footer clearfix">
						<button type="submit" name="categoryButton" class="btn btn-success pull-right">Ajouter</button>
					</div>
				</form>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">Ajouter un menu</div>
				<form method="POST">
					<div class="panel-body">
						<?php
							if(isset($menuMessage[$lib_errors]))
							{
								foreach($menuMessage as $type=>$messages)
								{
									$class = 'alert ';
									$class .= $lib_errors==$type?'alert-danger':'alert-success';
									foreach ($messages as $message)
									{
										echo '<div class="'.$class.'">'.$message.'</div>';
									}
								}
							}
						?>
						<div class="form-group">
							<label>Nom du menu</label>
							<input type="text" class="form-control" name="menuName" value="<?php echo $menuName; ?>" />
						</div>
						<div class="form-group">
							<label>Nom de la table</label>
							<input type="text" class="form-control" name="menuTable" value="<?php echo $menuTable; ?>" />
						</div>
						<div class="form-group">
							<label>Nom de l'icône</label>
							<input type="text" class="form-control" name="menuIcon" value="<?php echo $menuIcon; ?>" />
						</div>
						<div class="form-group">
							<label>Catégorie</label>
							<select name="menuCategory" class="form-control">
								<?php
									$menu_category_query = $db->prepare('SELECT `id`, `name` FROM site_category');
									$menu_category_query->execute();
								?>
								<?php while($menu_category = $menu_category_query->fetch()) { ?>
									<option value="<?php echo $menu_category['id']; ?>"><?php echo $menu_category['name']; ?></option>
								<?php } $menu_category_query->closeCursor(); ?>
							</select>
						</div>
						<div class="form-group">
							<label>Type</label>
							<select name="menuType" class="form-control">
								<option value="autre">Autre</option>
								<option value="jeuxvideo">Jeux Vidéo</option>
								<option value="livre">Livre</option>
								<option value="musique">Musique</option>
								<option value="video">Vidéo</option>
							</select>
						</div>
					</div>
					<div class="panel-footer clearfix">
						<button type="submit" name="menuButton" class="btn btn-success pull-right">Ajouter</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>