<?php
	if(!isset($_SESSION['username']))
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

	// Valeurs par défaut, remplacées si une autre valeur est saisie.
	foreach (array('mail', 'password', 'password1', 'password2', 'sex', 'birthday', 'country', 'website', 'facebook', 'twitter', 'googleplus', 'avatar') as $var)
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
?>
<script>document.title += ' - Profil'</script>
<div class="row">
	<?php if ($userid != $profile['id']) { ?>
		<div class="col-xs-12 col-sm-12 col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Information</div>
				<div class="panel-body">Désolé, mais ce membre n'existe pas.</div>
			</div>
		</div>
		</div></div><?php require_once('./template/bootstrap/includes/footer.php'); ?><?php require_once('./template/bootstrap/includes/javascript.php'); ?></body></html>
	<?php exit(); } ?>
	<div class="col-xs-12 col-sm-12 col-md-4">
		<div class="panel panel-default">
			<form method="POST">
				<div class="panel-body text-center">
					<img src="img/avatar/<?php echo $profile['avatar']; ?>" style="width:128px;" /><br/><br/>
					<strong><?php echo $profile['username']; ?></strong><br/>
					<small><?php echo rank($profile['rank']); ?></small><br/><br/>
					<div class="btn-group">
						<button type="button" class="btn btn-default"><i class="fa fa-envelope"></i></button>
						<button type="button" class="btn btn-primary"><i class="fa fa-facebook"></i></button>
						<button type="button" class="btn btn-info"><i class="fa fa-twitter"></i></button>
						<button type="button" class="btn btn-danger"><i class="fa fa-google-plus"></i></button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-8">
		<?php if ($user['id'] == $userid || $user['rank'] == '3') { ?>
			<div class="btn-group btn-group-justified">
				<div class="btn-group"><a href="<?php if (isset($_GET['userid'])) echo './?op=profile&userid='.$userid; else echo './?op=profile'; ?>" class="btn btn-default <?php if($tab == '1') echo 'active'; ?>">Profil</a></div>
				<div class="btn-group"><a href="<?php if (isset($_GET['userid'])) echo './?op=profile&tab=2&userid='.$userid; else echo './?op=profile&tab=2'; ?>" class="btn btn-default <?php if($tab == '2') echo 'active'; ?>">Modifier le profil</a></div>
				<div class="btn-group"><a href="<?php if (isset($_GET['userid'])) echo './?op=profile&tab=3&userid='.$userid; else echo './?op=profile&tab=3'; ?>" class="btn btn-default <?php if($tab == '3') echo 'active'; ?>">Modifier l'avatar</a></div>
			</div>
			<br/>
		<?php } ?>
		<?php if($tab == '1') { ?>
			<div class="panel panel-default">
				<div class="panel-heading">Profil</div>
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td width="30%"><strong>Genre</strong></td>
							<td><?php if($profile['sex'] != '0') echo sex($profile['sex']); ?></td>
						</tr>
						<tr>
							<td width="30%"><strong>Date de naissance</strong></td>
							<td><?php if ($profile['date_birthday'] != '0000-00-00') echo date('d/m/Y', strtotime($profile['date_birthday'])); ?></td>
						</tr>
						<tr>
							<td width="30%"><strong>Pays</strong></td>
							<td><?php echo $profile['country']; ?></td>
						</tr>
						<tr>
							<td width="30%"><strong>Date d'inscription</strong></td>
							<td><?php if ($profile['date_registration'] != '0000-00-00') echo date('d/m/Y', strtotime($profile['date_registration'])); ?></td>
						</tr>
						<tr>
							<td width="30%"><strong>Date de dernière visite</strong></td>
							<td><?php if ($profile['date_lastlogin'] != '0000-00-00 00:00:00') echo date('d/m/Y à H:i:s', strtotime($profile['date_lastlogin'])); ?></td>
						</tr>
						<tr>
							<td width="30%"><strong>Site web</strong></td>
							<td><a href="<?php echo $profile['url_website']; ?>" target="_blank"><?php echo $profile['url_website']; ?></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php } ?>
		<?php if ($user['id'] == $userid || $user['rank'] == '3') { ?>
			<?php if($tab == '2') { ?>
				<div class="panel panel-default">
					<div class="panel-heading">Modifier le profil</div>
					<form method="POST">
						<div class="panel-body">
							<?php
								if(isset($editMessage[$lib_errors]))
								{
									foreach($editMessage as $type=>$messages)
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
							<h4>Informations du compte</h4>
							<div class="form-group">
								<label>Nom d'utilisateur</label>
								<input type="text" class="form-control" value="<?php echo $profile['username']; ?>" disabled />
							</div>
							<div class="form-group">
								<label>Email</label>
								<input type="email" class="form-control" name="mail" value="<?php echo $mail; ?>" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<div class="form-group">
								<label>Mot de passe actuel</label>
								<input type="password" class="form-control" name="password" value="<?php echo $password; ?>" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<div class="form-group">
								<label>Nouveau mot de passe</label>
								<input type="password" class="form-control" name="password1" value="<?php echo $password1; ?>" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<div class="form-group">
								<label>Retapez le nouveau mot de passe</label>
								<input type="password" class="form-control" name="password2" value="<?php echo $password2; ?>" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<br/>
							<h4>Informations personnelles</h4>
							<div class="form-group">
								<label>Genre</label>
								<input type="hidden" name="sex" value="<?php echo $profile['sex']; ?>" />
								<div class="radio">
									<label><input type="radio" name="sex" value="1" <?php if ($profile['sex'] == '1') echo 'checked'; ?> <?php if ($user['rank'] == '1') echo 'disabled'; ?> /> Féminin</label>
									<label><input type="radio" name="sex" value="2" <?php if ($profile['sex'] == '2') echo 'checked'; ?> <?php if ($user['rank'] == '1') echo 'disabled'; ?> /> Masculin</label>
								</div>
							</div>
							<div class="form-group">
								<label>Date d'anniversaire</label>
								<input type="date" class="form-control" name="birthday" value="<?php echo $birthday; ?>" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<div class="form-group">
								<label>Pays</label>
								<input type="text" class="form-control" name="country" value="<?php echo $country; ?>" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<div class="form-group">
								<label>Site web</label>
								<input type="url" class="form-control" name="website" value="<?php echo $website; ?>" placeholder="http://" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<br/>
							<h4>Réseaux sociaux</h4>
							<div class="form-group">
								<label>Facebook</label>
								<input type="url" class="form-control" name="facebook" value="<?php echo $facebook; ?>" placeholder="http://" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<div class="form-group">
								<label>Twitter</label>
								<input type="url" class="form-control" name="twitter" value="<?php echo $twitter; ?>" placeholder="http://" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<div class="form-group">
								<label>Google+</label>
								<input type="url" class="form-control" name="googleplus" value="<?php echo $googleplus; ?>" placeholder="http://" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<br/>
						</div>
						<div class="panel-footer clearfix">
							<button type="submit" name="editButton" class="btn btn-success pull-right" <?php if ($user['rank'] == '1') echo 'disabled'; ?>>Modifier</button>
						</div>
					</form>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>