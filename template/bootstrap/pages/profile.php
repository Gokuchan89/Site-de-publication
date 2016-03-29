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
	
	/*
		=================================
		PROFIL
		=================================
	*/
	if (empty($_['mail']))
	{
		$mail = $profile['mail'];
	}
	if (empty($_['birthday']))
	{
		$birthday = $profile['date_birthday'];
	}
	if (empty($_['country']))
	{
		$country = $profile['country'];
	}
	if (empty($_['website']))
	{
		$website = $profile['url_website'];
	}
	if (empty($_['facebook']))
	{
		$facebook = $profile['url_facebook'];
	}
	if (empty($_['twitter']))
	{
		$twitter = $profile['url_twitter'];
	}
	if (empty($_['googleplus']))
	{
		$googleplus = $profile['url_googleplus'];
	}
	
	if(isset($_['editButton']))
	{
		// Vérification de la disponibilité de l'email
		$query = $db->prepare('SELECT COUNT(id) FROM site_user WHERE `mail` = :mail AND id != :id');
		$query->bindValue(':mail', $_['mail'], PDO::PARAM_STR);
		$query->bindValue(':id', $userid, PDO::PARAM_INT);
		$query->execute();
		$mail_free = $query->fetchColumn();
		$query->CloseCursor();
		if ($mail_free > 0 && empty($editMessage[$lib_errors]))
		{
			$editMessage[$lib_errors][] = 'Cette adresse email est déja utilisée par un autre membre.';
		}
		
		// Vérification de la présence du mot de passe actuel
		if(empty($_['password']) && !empty($_['password1']) && empty($editMessage[$lib_errors]))
		{
			$editMessage[$lib_errors][] = 'Veuillez indiquer le mot de passe actuel.';
		}
		
		// Vérification de la présence du nouveau mot de passe
		if(!empty($_['password']) && empty($_['password1']) && empty($_['password2']))
		{
			$editMessage[$lib_errors][] = 'Veuillez indiquer le nouveau mot de passe.';
		}
		
		// Vérification du mot de passe actuel
		if(!empty($_['password']) && md5($_['password']) != $profile['password'] && empty($editMessage[$lib_errors]))
		{
			$editMessage[$lib_errors][] = 'Le mot de passe actuel est faux.';
		}
		
		// Vérification des 2 nouveaux mots de passe
		if(!empty($_['password']) && empty($editMessage[$lib_errors]))
		{
			if ($_['password1'] != $_['password2'] && empty($editMessage[$lib_errors]))
			{
				$editMessage[$lib_errors][] = 'Le mot de passe et le mot de passe de confirmation ne sont pas identiques.';
			}
		}
	}
	
	if(isset($_['editButton']) && empty($editMessage[$lib_errors]))
	{
		$query = $db->prepare('UPDATE site_user SET password = :password, mail = :mail, date_birthday = :date_birthday, sex = :sex, url_website = :url_website, url_facebook = :url_facebook, url_twitter = :url_twitter, url_googleplus = :url_googleplus, country = :country WHERE id = :id');
		if(empty($_['password'])) $query->bindValue(':password', $profile['password'], PDO::PARAM_STR); else $query->bindValue(':password', md5($_['password1']), PDO::PARAM_STR);
		$query->bindValue(':mail', $_['mail'], PDO::PARAM_STR);
		$query->bindValue(':date_birthday', $_['birthday'], PDO::PARAM_INT);
		$query->bindValue(':sex', $_['sex'], PDO::PARAM_INT);
		$query->bindValue(':url_website', $_['website'], PDO::PARAM_STR);
		$query->bindValue(':url_facebook', $_['facebook'], PDO::PARAM_STR);
		$query->bindValue(':url_twitter', $_['twitter'], PDO::PARAM_STR);
		$query->bindValue(':url_googleplus', $_['googleplus'], PDO::PARAM_STR);
		$query->bindValue(':country', $_['country'], PDO::PARAM_STR);
		$query->bindValue(':id', $userid, PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		refresh($_SERVER['REQUEST_URI']);
	}
	
	/*
		=================================
		AVATAR
		=================================
	*/
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