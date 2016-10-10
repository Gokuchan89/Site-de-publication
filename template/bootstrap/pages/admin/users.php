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
		UTILISATEURS -> ADMIN
		=================================
	*/
	if (isset($_['user_edit_admin']))
	{
		$user = new User();
		$user->getUserDBID($_['user_edit_id']);
		$user->setAdmin($_['user_edit_admin']);
		$user->SaveUser();
	}

	/*
		=================================
		UTILISATEURS -> AJOUT
		=================================
	*/
	if (isset($_['userAddButton']) && $_['userAddButton'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_['user_add_username']) && !empty($_['user_add_email']) && !empty($_['user_add_password1']) && !empty($_['user_add_password2']))
		{
			$user_add_username = $_['user_add_username'];
			$user_add_password1 = $_['user_add_password1'];
			$user_add_password2 = $_['user_add_password2'];
			$user_add_email = $_['user_add_email'];
			$user_add_date = time();
			$user_add_admin = $_['user_add_admin'];
			$user_add_access = $_['user_add_access'];
			
			if ($user_add_password1 == $user_add_password2)
			{
				$user = new User();
				$user->setName($user_add_username);
				$user->setUsername($user_add_username);
				$user->setPassword($user_add_password1);
				$user->setEmail($user_add_email);
				$user->setDateregistration($user_add_date);
				$user->setAdmin($user_add_admin);
				$user->setAccess($user_add_access);
				$user->saveUser();
				
				$log = new Log_activite();
				$log->setUsername($_SESSION['name']);
				$log->setModule("Administration");
				$log->setAction("Utilisateurs");
				$log->setComment("L'utilisateur ".$user_add_username." a été créé par un administrateur.");
				$log->saveLog_activite();
			} else {
				$test[$lib_errors][] = "Les mots de passe ne correspondent pas.";
			}
		} else {
			$test[$lib_errors][] = "Il est nécessaire de fournir un identifiant, un email et un mot de passe pour l'utilisateur.";
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
				<li>Utilisateurs</li>
			</ol>
			<div class="btn-group btn-group-justified">
				<div class="btn-group"><a href="./?op=users" class="btn btn-default <?php if ($tab == 1) echo 'active'; ?>">Utilisateurs actifs</a></div>
				<div class="btn-group"><a href="./?op=users&tab=2" class="btn btn-default <?php if ($tab == 2) echo 'active'; ?>">Utilisateurs inactifs</a></div>
				<div class="btn-group"><a href="./?op=users&tab=3" class="btn btn-default <?php if ($tab == 3) echo 'active'; ?>">Ajouter un utilisateur</a></div>
			</div>
			<br/>
			<?php if ($tab == 1) { ?>
				<div class="panel panel-default">
					<!-- SUPPRESSION D'UN UTILISATEUR -->
					<div class="modal fade" id="ConfirmSupprUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header"><h4 class="modal-title" id="myModalLabel">Supprimer un utilisateur</h4></div>
								<div class="modal-body">
									<p>Voulez-vous vraiment supprimer cet utilisateur ?</p>
									<p>Une fois supprimée, celui-ci ne pourra plus se connecter.</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
									<button type="button" class="btn btn-primary" onclick="delUser()" data-dismiss="modal">Oui</button>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-heading">Utilisateurs actifs</div>
					<div class="panel-body table-responsive">
						<table class="table table-bordered table-striped" id="users_list">
							<thead>
								<tr>
									<th style="width:25%;">Identifiant</th>
									<th>Email</th>
									<th style="width:25%;">Privilègre</th>
									<th style="width:12%;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$users_active = new User();
									$liste = $users_active->getUserActiveList();
								?>
								<?php foreach($liste as $user_active) { ?>
									<?php if ($user_active['username'] != "anonymous") { ?>
										<tr>
											<td><img src="./img/avatars/<?php echo $user_active['avatar']; ?>" class="img-circle" style="max-height:30px;" alt="Avatar" /> <?php echo $user_active['username']; ?></td>
											<td><?php echo $user_active['email']; ?></td>
											<td>
												<form method="post" action="./?op=users">
													<input type="hidden" name="user_edit_id" value="<?php echo $user_active['id']; ?>" />
													<select class="form-control chosen" name="user_edit_admin" onchange="this.form.submit()" style="width:100%;" <?php if ($user_active['id'] == 2) echo 'disabled'; ?>>
														<option value="1" <?php if ($user_active['admin'] == 1) echo "selected"; ?>>Administrateur</option>
														<option value="0" <?php if ($user_active['admin'] == 0) echo "selected"; ?>>Membre</option>
													</select>
												</form>
											</td>
											<td>
												<div class="btn-toolbar" role="toolbar">
													<div class="btn-group">
														<a href="#" class="btn btn-success" title="Voir le profil de l'utilisateur"><i class="fa fa-search"></i></a>
														<button class="btn btn-primary" type="button" title="Modifier l'accès de l'utilisateur" onclick="user_edit_access(<?php echo $user_active['id']; ?>, <?php echo $tab; ?>)" <?php if ($user_active['id'] == 2) echo 'disabled'; ?>><i class="fa fa-check"></i></button>
														<button class="btn btn-danger" type="button" title="Supprimer l'utilisateur" onclick="user_del(<?php echo $user_active['id']; ?>)" <?php if ($user_active['id'] == 2) echo 'disabled'; ?>><i class="fa fa-trash-o"></i></button>
													</div>
												</div>
											</td>
										</tr>
									<?php } ?>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php } ?>
			<?php if ($tab == 2) { ?>
				<div class="panel panel-default">
					<!-- SUPPRESSION D'UN UTILISATEUR -->
					<div class="modal fade" id="ConfirmSupprUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header"><h4 class="modal-title" id="myModalLabel">Supprimer un utilisateur</h4></div>
								<div class="modal-body">
									<p>Voulez-vous vraiment supprimer cet utilisateur ?</p>
									<p>Une fois supprimée, celui-ci ne pourra plus se connecter.</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
									<button type="button" class="btn btn-primary" onclick="delUser()" data-dismiss="modal">Oui</button>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-heading">Utilisateurs inactifs</div>
					<div class="panel-body table-responsive">
						<table class="table table-bordered table-striped" id="users_list">
							<thead>
								<tr>
									<th style="width:25%;">Identifiant</th>
									<th>Email</th>
									<th style="width:25%;">Privilègre</th>
									<th style="width:12%;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$users_incative = new User();
									$liste = $users_incative->getUserInactiveList();
								?>
								<?php foreach($liste as $user_inactive) { ?>
									<?php if ($user_inactive['username'] != "anonymous") { ?>
										<tr>
											<td><img src="./img/avatars/<?php echo $user_inactive['avatar']; ?>" class="img-circle" style="max-height:30px;" alt="Avatar" /> <?php echo $user_inactive['username']; ?></td>
											<td><?php echo $user_inactive['email']; ?></td>
											<td>
												<form method="post" action="./?op=users&tab=2">
													<input type="hidden" name="user_edit_id" value="<?php echo $user_inactive['id']; ?>" />
													<select class="form-control chosen" name="user_edit_admin" onchange="this.form.submit()" style="width:100%;">
														<option value="1" <?php if ($user_inactive['admin'] == 1) echo "selected"; ?>>Administrateur</option>
														<option value="0" <?php if ($user_inactive['admin'] == 0) echo "selected"; ?>>Membre</option>
													</select>
												</form>
											</td>
											<td>
												<div class="btn-toolbar" role="toolbar">
													<div class="btn-group">
														<a href="#" class="btn btn-success" title="Voir le profil de l'utilisateur"><i class="fa fa-search"></i></a>
														<button class="btn btn-warning" type="button" title="Modifier l'accès de l'utilisateur" onclick="user_edit_access(<?php echo $user_inactive['id']; ?>, <?php echo $tab; ?>)"><i class="fa fa-times"></i></button>
														<button class="btn btn-danger" type="button" title="Supprimer l'utilisateur" onclick="user_del(<?php echo $user_inactive['id']; ?>)"><i class="fa fa-trash-o"></i></button>
													</div>
												</div>
											</td>
										</tr>
									<?php } ?>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php } ?>
			<?php if ($tab == 3) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">Ajouter un utilisateur</div>
					<form method="post" class="form-horizontal" action="./?op=users&tab=3" id="userAddForm">
						<input type="hidden" name="userAddButton" value="1">
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
								<label class="col-sm-4 control-label">Identifiant</label>
								<div class="col-sm-8"><input type="text" class="form-control" name="user_add_username" required /></div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email</label>
								<div class="col-sm-8"><input type="email" class="form-control" name="user_add_email" required /></div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Mot de passe</label>
								<div class="col-sm-8"><input type="password" class="form-control" name="user_add_password1" autocomplete="off" required /></div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Retapez le mot de passe</label>
								<div class="col-sm-8"><input type="password" class="form-control" name="user_add_password2" autocomplete="off" required /></div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Administrateur</label>
								<div class="col-sm-8" style="margin-top:5px;">
									<input type="radio" name="user_add_admin" value="0" checked> Non
									<input type="radio" name="user_add_admin" value="1"> Oui
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Actif</label>
								<div class="col-sm-8" style="margin-top:5px;">
									<input type="radio" name="user_add_access" value="0"> Non
									<input type="radio" name="user_add_access" value="1" checked> Oui
								</div>
							</div>
						</div>
						<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Ajouter</button></div>
					</form>
				</div>
			<?php } ?>
		</div>
		<?php include('./template/bootstrap/includes/footer.php'); ?>
		<?php include('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>