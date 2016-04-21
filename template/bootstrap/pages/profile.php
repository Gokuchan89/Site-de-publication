<?php
	if (!isset($_SESSION['username']))
	{
		header('location: ./');
		exit();
	}

	// Valeurs par défaut, remplacées si une autre valeur est saisie.
	foreach (array('profileMail', 'profilePassword', 'profilePassword1', 'profilePassword2', 'profileSex', 'profileBirthday', 'profileCountry', 'profileWebsite', 'profileFacebook', 'profileTwitter', 'profileGooglePlus', 'avatar') as $var)
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
		PROFIL
		=================================
	*/
	if (empty($_['profileMail']))
	{
		$profileMail = $profile['mail'];
	}
	if (empty($_['profileBirthday']))
	{
		if ($profile['date_birthday'] != '0000-00-00')
		{
			$profileBirthday = date('d/m/Y', strtotime($profile['date_birthday']));
		} else {
			$profileBirthday = 'jj/mm/aaaa';
		}
	}
	if (empty($_['profileCountry']))
	{
		$profileCountry = $profile['country'];
	}
	if (empty($_['profileWebsite']))
	{
		$profileWebsite = $profile['url_website'];
	}
	if (empty($_['profileFacebook']))
	{
		$profileFacebook = $profile['url_facebook'];
	}
	if (empty($_['profileTwitter']))
	{
		$profileTwitter = $profile['url_twitter'];
	}
	if (empty($_['profileGooglePlus']))
	{
		$profileGooglePlus = $profile['url_googleplus'];
	}

	if (isset($_['profileButton']))
	{
		$query = $db->prepare('SELECT `mail` FROM `site_user` WHERE `mail` = :mail AND `id` != :id');
		$query->bindValue(':mail', $_['profileMail'], PDO::PARAM_STR);
		$query->bindValue(':id', $userid, PDO::PARAM_INT);
		$query->execute();
		$profileVerifMail = $query->fetch();
		$query->CloseCursor();

		// Vérification que l'email n'est pas présent dans la table site_user
		if ($profileVerifMail['mail'] == $_['profileMail'])
		{
			$profileMessageMail = 'Cette adresse e-mail fait déjà l\'objet d\'un compte enregistré.';
			$i++;
		}

		// Vérification de la présence du mot de passe actuel
		if (empty($_['profilePassword']) && !empty($_['profilePassword1']) && $i == 0)
		{
			$profileMessagePassword = 'Veuillez indiquer le mot de passe actuel.';
			$i++;
		}

		// Vérification du mot de passe actuel
		if (!empty($_['profilePassword']) && md5($_['profilePassword']) != $profile['password'] && $i == 0)
		{
			$profileMessagePassword = 'Le mot de passe actuel n\'est pas valide.';
			$i++;
		}

		// Vérification de la présence du nouveau mot de passe
		if (!empty($_['profilePassword']) && empty($_['profilePassword1']) && empty($_['profilePassword2']) && $i == 0)
		{
			$profileMessagePassword1 = 'Veuillez indiquer le nouveau mot de passe.';
			$i++;
		}

		// Vérification des 2 nouveaux mots de passe
		if (!empty($_['profilePassword']) && $i == 0)
		{
			if ($_['profilePassword1'] != $_['profilePassword2'])
			{
				$profileMessagePassword2 = 'Les mots de passe ne correspondent pas.';
				$i++;
			}
		}
	}

	// Pas d'erreur, on met à jour les informations de l'utilisateur
	if (isset($_['profileButton']) && $i == 0)
	{
		$query = $db->prepare('UPDATE `site_user` SET `password` = :password, `mail` = :mail, `date_birthday` = :date_birthday, `sex` = :sex, `url_website` = :url_website, `url_facebook` = :url_facebook, `url_twitter` = :url_twitter, `url_googleplus` = :url_googleplus, `country` = :country WHERE `id` = :id');
		if (empty($_['profilePassword1'])) $query->bindValue(':password', $profile['password'], PDO::PARAM_STR); else $query->bindValue(':password', md5($_['profilePassword1']), PDO::PARAM_STR);
		$query->bindValue(':mail', $_['profileMail'], PDO::PARAM_STR);
		$query->bindValue(':date_birthday', date_birthday($_['profileBirthday']), PDO::PARAM_INT);
		if (empty($_['profileSex'])) $query->bindValue(':sex', $profile['sex'], PDO::PARAM_INT); else $query->bindValue(':sex', $_['profileSex'], PDO::PARAM_STR);
		$query->bindValue(':url_website', $_['profileWebsite'], PDO::PARAM_STR);
		$query->bindValue(':url_facebook', $_['profileFacebook'], PDO::PARAM_STR);
		$query->bindValue(':url_twitter', $_['profileTwitter'], PDO::PARAM_STR);
		$query->bindValue(':url_googleplus', $_['profileGooglePlus'], PDO::PARAM_STR);
		$query->bindValue(':country', $_['profileCountry'], PDO::PARAM_STR);
		$query->bindValue(':id', $userid, PDO::PARAM_INT);
		$query->execute();
		$query->CloseCursor();

		header('location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/*
		=================================
		AVATAR
		=================================
	*/
	$avatar_extension = '"jpg", "jpeg", "png", "gif"';

	if (isset($_['avatarButton']))
	{
		if ($_FILES['avatar']['size'])
		{

			$extensions_valides 		= array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF');
			$maxwidth 					= $config['avatarMaxWidth']; 		// Largeur de l'image
			$maxheight 					= $config['avatarMaxHeight']; 		// Hauteur de l'image
			$maxweight 					= $config['avatarMaxWeight']; 		// Poid de l'image

			// Vérification de l'extension de l'avatar
			$extension_upload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
			if (!in_array($extension_upload, $extensions_valides))
			{
				$avatarMessageExtension = 'L\'extension du fichier est incorrecte (<strong>'.$extension_upload.'</strong> au lieu de <strong>'.$avatar_extension.'</strong>).';
				$i++;
			}

			// Vérification de la taille de l'avatar
			$image_sizes = getimagesize($_FILES['avatar']['tmp_name']);
			if ($image_sizes[0] > $maxwidth || $image_sizes[1] > $maxheight)
			{
				$avatarMessageSize = 'La taille du fichier est incorrecte (<strong>'.$image_sizes[0].'x'.$image_sizes[1].'</strong> au lieu de <strong>'.$maxwidth.'x'.$maxheight.'</strong>).';
				$i++;
			}

			// Vérification du poid de l'avatar
			if ($_FILES['avatar']['size'] > $maxweight)
			{
				$avatarMessageWeight = 'Le poids du fichier est incorrect (<strong>'.intval($_FILES['avatar']['size'] / 1024).' ko</strong> au lieu de <strong>'.($maxweight / 1024).' ko</strong>).';
				$i++;
			}
		}
	}

	// Pas d'erreur, on modifie l'avatar de l'utilisateur
	if (isset($_['avatarButton']) && $i == 0)
	{
		if ($_FILES['avatar']['size'])
		{
			// Déplacement du fichier
			$avatar_name = (!empty($_FILES['avatar']['size']))?move_avatar($_FILES['avatar'], $profile['username']):'1.png';

			// Intégration des données dans la table user
			$query = $db->prepare('UPDATE `site_user` SET `avatar` = :avatar WHERE `id` = :id');
			$query->bindValue(':avatar', str_replace(' ','', $profile['username']).'.'.strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1)), PDO::PARAM_STR);
			$query->bindValue(':id', $userid, PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();

			header('location: '.$_SERVER['REQUEST_URI']);
			exit();
		}

		if (!$_FILES['avatar']['size'])
		{
			$query = $db->prepare('UPDATE `site_user` SET `avatar` = :avatar WHERE `id` = :id');
			$query->bindValue(':avatar', $_['avatar'], PDO::PARAM_STR);
			$query->bindValue(':id', $userid, PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();

			header('location: '.$_SERVER['REQUEST_URI']);
			exit();
		}
	}
?>
<script>document.title += " / Profil"</script>
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
					<img src="img/avatar/<?php echo $profile['avatar']; ?>" style="width:128px;" /><br /><br />
					<strong><?php echo $profile['username']; ?></strong><br />
					<small><?php echo rank($profile['rank']); ?></small><br /><br />
					<div class="btn-group">
						<?php if (!empty($profile['mail'])) echo '<a href="mailto:'.$profile['mail'].'" class="btn btn-default"><i class="fa fa-envelope"></i></a>'; ?>
						<?php if (!empty($profile['url_facebook'])) echo '<a href="'.$profile['url_facebook'].'" class="btn btn-primary" target="_blank"><i class="fa fa-facebook"></i></a>'; ?>
						<?php if (!empty($profile['url_twitter'])) echo '<a href="'.$profile['url_twitter'].'" class="btn btn-info" target="_blank"><i class="fa fa-twitter"></i></a>'; ?>
						<?php if (!empty($profile['url_googleplus'])) echo '<a href="'.$profile['url_googleplus'].'" class="btn btn-danger" target="_blank"><i class="fa fa-google-plus"></i></a>'; ?>
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
			<br />
		<?php } ?>
		<!-- INFORMATIONS DU PROFIL -->
		<?php if ($tab == '1') { ?>
			<div class="panel panel-default">
				<div class="panel-heading">Profil</div>
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td width="30%"><strong>Genre</strong></td>
							<td><?php if ($profile['sex'] != '0') echo sex($profile['sex']); ?></td>
						</tr>
						<tr>
							<td width="30%"><strong>Date de naissance</strong></td>
							<td><?php if ($profile['date_birthday'] != '0000-00-00') echo date('d/m/Y', strtotime($profile['date_birthday'])); ?></td>
						</tr>
						<tr>
							<td width="30%"><strong>Pays</strong></td>
							<td><?php echo str_replace('_', ' ', $profile['country']); ?></td>
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
			<!-- MODIFICATION DU PROFIL -->
			<?php if ($tab == '2') { ?>
				<div class="panel panel-default">
					<div class="panel-heading">Modifier le profil</div>
					<form method="POST">
						<div class="panel-body">
							<h4>Informations du compte</h4>
							<div class="form-group">
								<label>Nom d'utilisateur</label>
								<input type="text" class="form-control" value="<?php echo $profile['username']; ?>" disabled />
							</div>
							<div class="form-group <?php if (isset($profileMessageMail)) echo 'has-error'; ?>">
								<label>Email</label>
								<input type="email" class="form-control" name="profileMail" value="<?php echo $profileMail; ?>" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<?php if (isset($profileMessageMail)) echo '<div class="alert alert-danger">'.$profileMessageMail.'</div>'; ?>
							<div class="form-group <?php if (isset($profileMessagePassword)) echo 'has-error'; ?>">
								<label>Mot de passe actuel</label>
								<input type="password" class="form-control" name="profilePassword" value="<?php echo $profilePassword; ?>" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<?php if (isset($profileMessagePassword)) echo '<div class="alert alert-danger">'.$profileMessagePassword.'</div>'; ?>
							<div class="form-group <?php if (isset($profileMessagePassword1) || isset($profileMessagePassword2)) echo 'has-error'; ?>">
								<label>Nouveau mot de passe</label>
								<input type="password" class="form-control" name="profilePassword1" value="<?php echo $profilePassword1; ?>" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<?php if (isset($profileMessagePassword1)) echo '<div class="alert alert-danger">'.$profileMessagePassword1.'</div>'; ?>
							<div class="form-group <?php if (isset($profileMessagePassword2)) echo 'has-error'; ?>">
								<label>Retapez le nouveau mot de passe</label>
								<input type="password" class="form-control" name="profilePassword2" value="<?php echo $profilePassword2; ?>" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<?php if (isset($profileMessagePassword2)) echo '<div class="alert alert-danger">'.$profileMessagePassword2.'</div>'; ?>
							<br />
							<h4>Informations personnelles</h4>
							<div class="form-group">
								<label>Genre</label>
								<div class="radio">
									<label><input type="radio" name="profileSex" value="1" <?php if ($profile['sex'] == '1') echo 'checked'; ?> <?php if ($user['rank'] == '1') echo 'disabled'; ?> /> Féminin</label>
									<label><input type="radio" name="profileSex" value="2" <?php if ($profile['sex'] == '2') echo 'checked'; ?> <?php if ($user['rank'] == '1') echo 'disabled'; ?> /> Masculin</label>
								</div>
							</div>
							<div class="form-group">
								<label>Date d'anniversaire</label>
								<input type="text" class="form-control" name="profileBirthday" value="<?php echo $profileBirthday; ?>" id="datepicker" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<div class="form-group">
								<label>Pays</label>
								<select class="form-control select2-pays" name="profileCountry" <?php if ($user['rank'] == '1') echo 'disabled'; ?>>
									<option value=""></option>
									<optgroup label="Europe">
										<option value="Allemagne" <?php if ($profileCountry == 'Allemagne') echo 'selected'; ?>>Allemagne</option>
										<option value="Albanie" <?php if ($profileCountry == 'Albanie') echo 'selected'; ?>>Albanie</option>
										<option value="Andorre" <?php if ($profileCountry == 'Andorre') echo 'selected'; ?>>Andorre</option>
										<option value="Autriche" <?php if ($profileCountry == 'Autriche') echo 'selected'; ?>>Autriche</option>
										<option value="Biélorussie" <?php if ($profileCountry == 'Biélorussie') echo 'selected'; ?>>Biélorussie</option>
										<option value="Belgique" <?php if ($profileCountry == 'Belgique') echo 'selected'; ?>>Belgique</option>
										<option value="Bosnie-Herzégovine" <?php if ($profileCountry == 'Bosnie-Herzégovine') echo 'selected'; ?>>Bosnie-Herzégovine</option>
										<option value="Bulgarie" <?php if ($profileCountry == 'Bulgarie') echo 'selected'; ?>>Bulgarie</option>
										<option value="Croatie" <?php if ($profileCountry == 'Croatie') echo 'selected'; ?>>Croatie</option>
										<option value="danemark" <?php if ($profileCountry == 'Danemark') echo 'selected'; ?>>Danemark</option>
										<option value="espagne" <?php if ($profileCountry == 'Espagne') echo 'selected'; ?>>Espagne</option>
										<option value="estonie" <?php if ($profileCountry == 'Estonie') echo 'selected'; ?>>Estonie</option>
										<option value="finlande" <?php if ($profileCountry == 'Finlande') echo 'selected'; ?>>Finlande</option>
										<option value="France" <?php if ($profileCountry == 'France') echo 'selected'; ?>>France</option>
										<option value="Grèce" <?php if ($profileCountry == 'Grèce') echo 'selected'; ?>>Grèce</option>
										<option value="Hongrie" <?php if ($profileCountry == 'Hongrie') echo 'selected'; ?>>Hongrie</option>
										<option value="Irlande" <?php if ($profileCountry == 'Irlande') echo 'selected'; ?>>Irlande</option>
										<option value="Islande" <?php if ($profileCountry == 'Islande') echo 'selected'; ?>>Islande</option>
										<option value="Italie" <?php if ($profileCountry == 'Italie') echo 'selected'; ?>>Italie</option>
										<option value="Lettonie" <?php if ($profileCountry == 'Lettonie') echo 'selected'; ?>>Lettonie</option>
										<option value="Liechtenstein" <?php if ($profileCountry == 'Liechtenstein') echo 'selected'; ?>>Liechtenstein</option>
										<option value="Lituanie" <?php if ($profileCountry == 'Lituanie') echo 'selected'; ?>>Lituanie</option>
										<option value="Luxembourg" <?php if ($profileCountry == 'Luxembourg') echo 'selected'; ?>>Luxembourg</option>
										<option value="Ex-République_Yougoslave_de_Macédoine" <?php if ($profileCountry == 'Ex-République_Yougoslave_de_Macédoine') echo 'selected'; ?>>Ex-République Yougoslave de Macédoine</option>
										<option value="Malte" <?php if ($profileCountry == 'Malte') echo 'selected'; ?>>Malte</option>
										<option value="Moldavie" <?php if ($profileCountry == 'Moldavie') echo 'selected'; ?>>Moldavie</option>
										<option value="Monaco" <?php if ($profileCountry == 'Monaco') echo 'selected'; ?>>Monaco</option>
										<option value="Norvège" <?php if ($profileCountry == 'Norvège') echo 'selected'; ?>>Norvège</option>
										<option value="Pays-Bas" <?php if ($profileCountry == 'Pays-Bas') echo 'selected'; ?>>Pays-Bas</option>
										<option value="Pologne" <?php if ($profileCountry == 'Pologne') echo 'selected'; ?>>Pologne</option>
										<option value="Portugal" <?php if ($profileCountry == 'Portugal') echo 'selected'; ?>>Portugal</option>
										<option value="Roumanie" <?php if ($profileCountry == 'Roumanie') echo 'selected'; ?>>Roumanie</option>
										<option value="Royaume-Uni" <?php if ($profileCountry == 'Royaume-Uni') echo 'selected'; ?>>Royaume-Uni</option>
										<option value="Russie" <?php if ($profileCountry == 'Russie') echo 'selected'; ?>>Russie</option>
										<option value="Saint-Marin" <?php if ($profileCountry == 'Saint-Marin') echo 'selected'; ?>>Saint-Marin</option>
										<option value="Serbie-et-Monténégro" <?php if ($profileCountry == 'Serbie-et-Monténégro') echo 'selected'; ?>>Serbie-et-Monténégro</option>
										<option value="Slovaquie" <?php if ($profileCountry == 'Slovaquie') echo 'selected'; ?>>Slovaquie</option>
										<option value="Slovénie" <?php if ($profileCountry == 'Slovénie') echo 'selected'; ?>>Slovénie</option>
										<option value="Suède" <?php if ($profileCountry == 'Suède') echo 'selected'; ?>>Suède</option>
										<option value="Suisse" <?php if ($profileCountry == 'Suisse') echo 'selected'; ?>>Suisse</option>
										<option value="République_Tchèque" <?php if ($profileCountry == 'République_Tchèque') echo 'selected'; ?>>République Tchèque</option>
										<option value="Ukraine" <?php if ($profileCountry == 'Ukraine') echo 'selected'; ?>>Ukraine</option>
										<option value="Vatican" <?php if ($profileCountry == 'Vatican') echo 'selected'; ?>>Vatican</option>
									</optgroup>
									<optgroup label="Afrique">
										<option value="Afrique_du_Sud" <?php if ($profileCountry == 'Afrique_du_Sud') echo 'selected'; ?>>Afrique du Sud</option>
										<option value="Algérie" <?php if ($profileCountry == 'Algérie') echo 'selected'; ?>>Algérie</option>
										<option value="Angola" <?php if ($profileCountry == 'Angola') echo 'selected'; ?>>Angola</option>
										<option value="Bénin" <?php if ($profileCountry == 'Bénin') echo 'selected'; ?>>Bénin</option>
										<option value="Botswana" <?php if ($profileCountry == 'Botswana') echo 'selected'; ?>>Botswana</option>
										<option value="Burkina" <?php if ($profileCountry == 'Burkina') echo 'selected'; ?>>Burkina</option>
										<option value="Burundi" <?php if ($profileCountry == 'Burundi') echo 'selected'; ?>>Burundi</option>
										<option value="Cameroun" <?php if ($profileCountry == 'Cameroun') echo 'selected'; ?>>Cameroun</option>
										<option value="Cap-Vert" <?php if ($profileCountry == 'Cap-Vert') echo 'selected'; ?>>Cap-Vert</option>
										<option value="République_Centre-Africaine" <?php if ($profileCountry == 'République_Centre-Africaine') echo 'selected'; ?>>République Centre-Africaine</option>
										<option value="Comores" <?php if ($profileCountry == 'Comores') echo 'selected'; ?>>Comores</option>
										<option value="République_Démocratique_du_Congo" <?php if ($profileCountry == 'République_Démocratique_du_Congo') echo 'selected'; ?>>République Démocratique du Congo</option>
										<option value="Congo" <?php if ($profileCountry == 'Congo') echo 'selected'; ?>>Congo</option>
										<option value="Côte_d'Ivoire" <?php if ($profileCountry == 'Côte_d\'Ivoire') echo 'selected'; ?>>Côte d'Ivoire</option>
										<option value="Djibouti" <?php if ($profileCountry == 'Djibouti') echo 'selected'; ?>>Djibouti</option>
										<option value="Égypte" <?php if ($profileCountry == 'Égypte') echo 'selected'; ?>>Égypte</option>
										<option value="Éthiopie" <?php if ($profileCountry == 'Éthiopie') echo 'selected'; ?>>Éthiopie</option>
										<option value="Érythrée" <?php if ($profileCountry == 'Érythrée') echo 'selected'; ?>>Érythrée</option>
										<option value="Gabon" <?php if ($profileCountry == 'Gabon') echo 'selected'; ?>>Gabon</option>
										<option value="Gambie" <?php if ($profileCountry == 'Gambie') echo 'selected'; ?>>Gambie</option>
										<option value="Ghana" <?php if ($profileCountry == 'Ghana') echo 'selected'; ?>>Ghana</option>
										<option value="Guinée" <?php if ($profileCountry == 'Guinée') echo 'selected'; ?>>Guinée</option>
										<option value="Guinée-Bisseau" <?php if ($profileCountry == 'Guinée-Bisseau') echo 'selected'; ?>>Guinée-Bisseau</option>
										<option value="Guinée_Équatoriale" <?php if ($profileCountry == 'Guinée_Équatoriale') echo 'selected'; ?>>Guinée Équatoriale</option>
										<option value="Kenya" <?php if ($profileCountry == 'Kenya') echo 'selected'; ?>>Kenya</option>
										<option value="Lesotho" <?php if ($profileCountry == 'Lesotho') echo 'selected'; ?>>Lesotho</option>
										<option value="Liberia" <?php if ($profileCountry == 'Liberia') echo 'selected'; ?>>Liberia</option>
										<option value="Libye" <?php if ($profileCountry == 'Libye') echo 'selected'; ?>>Libye</option>
										<option value="Madagascar" <?php if ($profileCountry == 'Madagascar') echo 'selected'; ?>>Madagascar</option>
										<option value="Malawi" <?php if ($profileCountry == 'Malawi') echo 'selected'; ?>>Malawi</option>
										<option value="Mali" <?php if ($profileCountry == 'Mali') echo 'selected'; ?>>Mali</option>
										<option value="Maroc" <?php if ($profileCountry == 'Maroc') echo 'selected'; ?>>Maroc</option>
										<option value="Maurice" <?php if ($profileCountry == 'Maurice') echo 'selected'; ?>>Maurice</option>
										<option value="Mauritanie" <?php if ($profileCountry == 'Mauritanie') echo 'selected'; ?>>Mauritanie</option>
										<option value="Mozambique" <?php if ($profileCountry == 'Mozambique') echo 'selected'; ?>>Mozambique</option>
										<option value="Namibie" <?php if ($profileCountry == 'Namibie') echo 'selected'; ?>>Namibie</option>
										<option value="Niger" <?php if ($profileCountry == 'Niger') echo 'selected'; ?>>Niger</option>
										<option value="Nigéria" <?php if ($profileCountry == 'Nigéria') echo 'selected'; ?>>Nigéria</option>
										<option value="Ouganda" <?php if ($profileCountry == 'Ouganda') echo 'selected'; ?>>Ouganda</option>
										<option value="Rwanda" <?php if ($profileCountry == 'Rwanda') echo 'selected'; ?>>Rwanda</option>
										<option value="Sao_Tomé-et-Principe" <?php if ($profileCountry == 'Sao_Tomé-et-Principe') echo 'selected'; ?>>Sao Tomé-et-Principe</option>
										<option value="Sénégal" <?php if ($profileCountry == 'Sénégal') echo 'selected'; ?>>Sénégal</option>
										<option value="Seychelles" <?php if ($profileCountry == 'Seychelles') echo 'selected'; ?>>Seychelles</option>
										<option value="Sierra" <?php if ($profileCountry == 'Sierra') echo 'selected'; ?>>Sierra</option>
										<option value="Somalie" <?php if ($profileCountry == 'Somalie') echo 'selected'; ?>>Somalie</option>
										<option value="Soudan" <?php if ($profileCountry == 'Soudan') echo 'selected'; ?>>Soudan</option>
										<option value="Swaziland" <?php if ($profileCountry == 'Swaziland') echo 'selected'; ?>>Swaziland</option>
										<option value="Tanzanie" <?php if ($profileCountry == 'Tanzanie') echo 'selected'; ?>>Tanzanie</option>
										<option value="Tchad" <?php if ($profileCountry == 'Tchad') echo 'selected'; ?>>Tchad</option>
										<option value="Togo" <?php if ($profileCountry == 'Togo') echo 'selected'; ?>>Togo</option>
										<option value="Tunisie <?php if ($profileCountry == 'Tunisie') echo 'selected'; ?>">Tunisie</option>
										<option value="Zambie" <?php if ($profileCountry == 'Zambie') echo 'selected'; ?>>Zambie</option>
										<option value="Zimbabwe" <?php if ($profileCountry == 'Zimbabwe') echo 'selected'; ?>>Zimbabwe</option>
									</optgroup>
									<optgroup label="Amérique">
										<option value="antiguaEtBarbuda">Antigua-et-Barbuda</option>
										<option value="Argentine">Argentine</option>
										<option value="bahamas">Bahamas</option>
										<option value="barbade">Barbade</option>
										<option value="belize">Belize</option>
										<option value="bolivie">Bolivie</option>
										<option value="bresil">Brésil</option>
										<option value="canada">Canada</option>
										<option value="chili">Chili</option>
										<option value="colombie">Colombie</option>
										<option value="costaRica">Costa Rica</option>
										<option value="cuba">Cuba</option>
										<option value="republiqueDominicaine">République Dominicaine</option>
										<option value="dominique">Dominique</option>
										<option value="equateur">Équateur</option>
										<option value="etatsUnis">États Unis</option>
										<option value="grenade">Grenade</option>
										<option value="guatemala">Guatemala</option>
										<option value="guyana">Guyana</option>
										<option value="haiti">Haïti</option>
										<option value="honduras">Honduras</option>
										<option value="jamaique">Jamaïque</option>
										<option value="mexique">Mexique</option>
										<option value="nicaragua">Nicaragua</option>
										<option value="panama">Panama</option>
										<option value="paraguay">Paraguay</option>
										<option value="perou">Pérou</option>
										<option value="saintCristopheEtNieves">Saint-Cristophe-et-Niévès</option>
										<option value="sainteLucie">Sainte-Lucie</option>
										<option value="saintVincentEtLesGrenadines">Saint-Vincent-et-les-Grenadines</option>
										<option value="salvador">Salvador</option>
										<option value="suriname">Suriname</option>
										<option value="triniteEtTobago">Trinité-et-Tobago</option>
										<option value="uruguay">Uruguay</option>
										<option value="venezuela">Venezuela</option>
									</optgroup>
									<optgroup label="Asie">
										<option value="afghanistan">Afghanistan</option>
										<option value="arabieSaoudite">Arabie Saoudite</option>
										<option value="armenie">Arménie</option>
										<option value="azerbaidjan">Azerbaïdjan</option>
										<option value="bahrein">Bahreïn</option>
										<option value="bangladesh">Bangladesh</option>
										<option value="bhoutan">Bhoutan</option>
										<option value="birmanie">Birmanie</option>
										<option value="brunei">Brunéi</option>
										<option value="cambodge">Cambodge</option>
										<option value="chine">Chine</option>
										<option value="coreeDuSud">Corée Du Sud</option>
										<option value="coreeDuNord">Corée Du Nord</option>
										<option value="emiratsArabeUnis">Émirats Arabe Unis</option>
										<option value="georgie">Géorgie</option>
										<option value="inde">Inde</option>
										<option value="indonesie">Indonésie</option>
										<option value="iraq">Iraq</option>
										<option value="iran">Iran</option>
										<option value="israel">Israël</option>
										<option value="japon">Japon</option>
										<option value="jordanie">Jordanie</option>
										<option value="kazakhstan">Kazakhstan</option>
										<option value="kirghistan">Kirghistan</option>
										<option value="koweit">Koweït</option>
										<option value="laos">Laos</option>
										<option value="liban">Liban</option>
										<option value="malaisie">Malaisie</option>
										<option value="maldives">Maldives</option>
										<option value="mongolie">Mongolie</option>
										<option value="nepal">Népal</option>
										<option value="oman">Oman</option>
										<option value="ouzbekistan">Ouzbékistan</option>
										<option value="pakistan">Pakistan</option>
										<option value="philippines">Philippines</option>
										<option value="qatar">Qatar</option>
										<option value="singapour">Singapour</option>
										<option value="sriLanka">Sri Lanka</option>
										<option value="syrie">Syrie</option>
										<option value="tadjikistan">Tadjikistan</option>
										<option value="taiwan">Taïwan</option>
										<option value="thailande">Thaïlande</option>
										<option value="timorOriental">Timor oriental</option>
										<option value="turkmenistan">Turkménistan</option>
										<option value="turquie">Turquie</option>
										<option value="vietNam">Viêt Nam</option>
										<option value="yemen">Yemen</option>
									</optgroup>
									<optgroup label="Océanie">
										<option value="Australie" <?php if ($profileCountry == 'Australie') echo 'selected'; ?>>Australie</option>
										<option value="Fidji" <?php if ($profileCountry == 'Fidji') echo 'selected'; ?>>Fidji</option>
										<option value="Kiribati" <?php if ($profileCountry == 'Kiribati') echo 'selected'; ?>>Kiribati</option>
										<option value="Marshall" <?php if ($profileCountry == 'Marshall') echo 'selected'; ?>>Marshall</option>
										<option value="Micronésie" <?php if ($profileCountry == 'Micronésie') echo 'selected'; ?>>Micronésie</option>
										<option value="Nauru" <?php if ($profileCountry == 'Nauru') echo 'selected'; ?>>Nauru</option>
										<option value="Nouvelle-Zélande" <?php if ($profileCountry == 'Nouvelle-Zélande') echo 'selected'; ?>>Nouvelle-Zélande</option>
										<option value="Palaos" <?php if ($profileCountry == 'Palaos') echo 'selected'; ?>>Palaos</option>
										<option value="Papouasie-Nouvelle-Guinée" <?php if ($profileCountry == 'Papouasie-Nouvelle-Guinée') echo 'selected'; ?>>Papouasie-Nouvelle-Guinée</option>
										<option value="Salomon" <?php if ($profileCountry == 'Salomon') echo 'selected'; ?>>Salomon</option>
										<option value="Samoa" <?php if ($profileCountry == 'Samoa') echo 'selected'; ?>>Samoa</option>
										<option value="Tonga" <?php if ($profileCountry == 'Tonga') echo 'selected'; ?>>Tonga</option>
										<option value="Tuvalu" <?php if ($profileCountry == 'Tuvalu') echo 'selected'; ?>>Tuvalu</option>
										<option value="Vanuatu" <?php if ($profileCountry == 'Vanuatu') echo 'selected'; ?>>Vanuatu</option>
									</optgroup>
								</select>
							</div>
							<div class="form-group">
								<label>Site web</label>
								<input type="url" class="form-control" name="profileWebsite" value="<?php echo $profileWebsite; ?>" placeholder="http://" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<br />
							<h4>Réseaux sociaux</h4>
							<div class="form-group">
								<label>Facebook</label>
								<input type="url" class="form-control" name="profileFacebook" value="<?php echo $profileFacebook; ?>" placeholder="http://" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<div class="form-group">
								<label>Twitter</label>
								<input type="url" class="form-control" name="profileTwitter" value="<?php echo $profileTwitter; ?>" placeholder="http://" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<div class="form-group">
								<label>Google+</label>
								<input type="url" class="form-control" name="profileGooglePlus" value="<?php echo $profileGooglePlus; ?>" placeholder="http://" <?php if ($user['rank'] == '1') echo 'disabled'; ?> />
							</div>
							<br />
						</div>
						<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right" name="profileButton" <?php if ($user['rank'] == '1') echo 'disabled'; ?>>Modifier</button></div>
					</form>
				</div>
			<?php } ?>
			<!-- MODIFICATION DE L'AVATAR -->
			<?php if ($tab == '3') { ?>
				<div class="panel panel-default">
					<div class="panel-heading">Modifier l'avatar</div>
					<form method="POST" enctype="multipart/form-data">
						<div class="panel-body">
							<?php
								if (isset($avatarMessageExtension)) echo '<div class="alert alert-danger">'.$avatarMessageExtension.'</div>';
								if (isset($avatarMessageSize)) echo '<div class="alert alert-danger">'.$avatarMessageSize.'</div>';
								if (isset($avatarMessageWeight)) echo '<div class="alert alert-danger">'.$avatarMessageWeight.'</div>';
							?>
							<h4>Votre avatar</h4>
							<div class="form-group">
								<div class="row">
									<div class="col-xs-12 col-sm-4 col-md-3"><label><small class="help-block"><?php echo 'Le fichier doit être au format <strong>'.$avatar_extension.'</strong>, de taille <strong>'.$config['avatarMaxWidth'].'x'.$config['avatarMaxHeight'].'</strong> et avoir un poids de <strong>'.($config['avatarMaxWeight'] / 1024).' ko</strong> maximum.'; ?></small></label></div>
									<div class="col-xs-12 col-sm-8 col-md-9">
										<div class="fileinput fileinput-new" data-provides="fileinput">
											<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width:100%;height:170px;"></div>
											<div>
												<span class="btn btn-default btn-file" <?php if ($user['rank'] == 1) echo 'disabled'; ?>>
													<span class="fileinput-new">Sélectionner une image</span>
													<span class="fileinput-exists">Modifier</span>
													<input type="file" name="avatar" <?php if ($user['rank'] == 1) echo 'disabled'; ?> />
												</span>
												<a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Supprimer</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<br />
							<h4>Avatar prédéfini</h4>
							<div class="form-group text-center">
								<div class="col-xs-12 col-sm-12 col-md-12">
									<?php
										for ($i=1; $i<=6; $i++)
										{
											if ($profile['avatar'] == $i.'.png') $checked = 'checked'; else $checked = '';
											echo '<label class="avatar-theme"><input type="radio" name="avatar" value="'.$i.'.png" '.$checked.' /><img src="img/avatar/'.$i.'.png" style="width:114px;" title="avatar" /></label>';
										}
									?>
								</div>
							</div>
						</div>
						<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right" name="avatarButton" <?php if ($user['rank'] == '1') echo 'disabled'; ?>>Modifier</button></div>
					</form>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>