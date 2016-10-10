<?php
	if (!isset($_SESSION['username']) || $_SESSION['username'] == "anonymous")
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
	$lib_success = "Succès";

	/*
		=================================
		PROFIL -> MODIFICATION
		=================================
	*/
	if (isset($_['profileEditButton']) && $_['profileEditButton'] == 1 && empty($test[$lib_errors]))
	{
		if ($_['profile_edit_password1'] == $_['profile_edit_password2'])
		{
			$user = new User();
			$user->getUserDBUsername($_SESSION['username']);
			$user->setEmail($_['profile_edit_email']);
			if ($_['profile_edit_password1'] != "")
			{
				$user->setPassword($_['profile_edit_password1']);
			}
			if ($_['profile_edit_datebirthday'] != "")
			{
				$profile_edit_datebirthday = Functions::date_birthday($_['profile_edit_datebirthday']);
				$user->setDatebirthday($profile_edit_datebirthday);
			} else {
				$user->setDatebirthday(NULL);
			}
			if ($_['profile_edit_country'] != "")
			{
				$user->setCountry($_['profile_edit_country']);
			} else {
				$user->setCountry(NULL);
			}
			if ($_['profile_edit_urlwebsite'] != "")
			{
				$user->setUrlwebsite($_['profile_edit_urlwebsite']);
			} else {
				$user->setUrlwebsite(NULL);
			}
			$user->saveUser();
			
			$test[$lib_success][] = "Vos informations ont bien été modifiées.";
		} else {
			$test[$lib_errors][] = "Les mots de passe ne correspondent pas.";
		}
	}

	/*
		=================================
		PROFIL -> MODIFICATION -> AVATAR
		=================================
	*/
	$avatar_extension = "\"jpg\", \"jpeg\", \"png\", \"gif\"";

	if (isset($_['avatarEditButton']) && $_['avatarEditButton'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_FILES['avatar_edit']['size']) || !empty($_['avatar_edit']))
		{
			if ($_FILES['avatar_edit']['size'])
			{
				$setting_avatar_width = new Setting();
				$setting_avatar_width->getSettingDBKey('avatar_width');
				
				$setting_avatar_height = new Setting();
				$setting_avatar_height->getSettingDBKey('avatar_height');
				
				$setting_avatar_weight = new Setting();
				$setting_avatar_weight->getSettingDBKey('avatar_weight');
				
				$extensions_valides 		= array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF');
				$avatar_width 				= $setting_avatar_width->getValue(); 		// Largeur de l'image
				$avatar_height 				= $setting_avatar_height->getValue(); 		// Hauteur de l'image
				$avatar_weight 				= $setting_avatar_weight->getValue(); 		// Poid de l'image

				// Vérification de l'extension de l'avatar
				$extension_upload = strtolower(substr(strrchr($_FILES['avatar_edit']['name'], '.'), 1));
				if (in_array($extension_upload, $extensions_valides))
				{
					// Vérification de la taille de l'avatar
					$image_sizes = getimagesize($_FILES['avatar_edit']['tmp_name']);
					if ($image_sizes[0] < $avatar_width || $image_sizes[1] < $avatar_height)
					{
						// Vérification du poids de l'avatar
						if ($_FILES['avatar_edit']['size'] < $avatar_weight)
						{
							// Déplacement du fichier
							$avatar_name = (!empty($_FILES['avatar_edit']['size']))?Functions::move_avatar($_FILES['avatar_edit'], $_SESSION['username']):"1.png";

							// Intégration des données dans la table site_user
							$avatar = new User();
							$avatar->getUserDBUsername($_SESSION['username']);
							$avatar->setAvatar(str_replace(" ", "", $_SESSION['username']).'.'.strtolower(substr(strrchr($_FILES['avatar_edit']['name'], "."), 1)));
							$avatar->saveUser();
							
							$test[$lib_success][] = "Votre avatar à bien été modifié.";
						} else {
							$test[$lib_errors][] = "Le poids du fichier est incorrect (<strong>".intval($_FILES['avatar_edit']['size'] / 1024)." ko</strong> au lieu de <strong>".($avatar_weight / 1024)." ko</strong>).";
						}
					} else {
						$test[$lib_errors][] = "La taille du fichier est incorrecte (<strong>".$image_sizes[0]."x".$image_sizes[1]."</strong> au lieu de <strong>".$avatar_width."x".$avatar_height."</strong>).";
					}
				} else {
					$test[$lib_errors][] = "L'extension du fichier est incorrecte (<strong>".$extension_upload."</strong> au lieu de <strong>".$avatar_extension."</strong>).";
				}
			} else {
				$avatar = new User();
				$avatar->getUserDBUsername($_SESSION['username']);
				$avatar->setAvatar($_['avatar_edit']);
				$avatar->saveUser();
							
				$test[$lib_success][] = "Votre avatar à bien été modifié.";
			}
		} else {
			$test[$lib_errors][] = "Il est nécessaire de sélectionner un avatar.";
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
				<li>Profil</li>
			</ol>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-4">
					<div class="panel panel-default">
						<div class="panel-body text-center">
							<img src="./img/avatars/<?php $user_avatar = new User(); $user_avatar->getUserDBUsername($_SESSION['username']); echo $user_avatar->getAvatar(); ?>" style="width:128px;" /><br /><br />
							<strong><?php $user_username = new User(); $user_username->getUserDBUsername($_SESSION['username']); echo $user_username->getUsername(); ?></strong><br />
							<small><?php $user_admin = new User(); $user_admin->getUserDBUsername($_SESSION['username']); if ($user_admin->getAdmin() == 1) echo 'Administrateur'; ?></small><br /><br />
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-8">
					<div class="btn-group btn-group-justified">
						<div class="btn-group"><a href="./?op=profile" class="btn btn-default <?php if ($tab == 1) echo 'active'; ?>">Informations</a></div>
						<div class="btn-group"><a href="./?op=profile&tab=2" class="btn btn-default <?php if ($tab == 2) echo 'active'; ?>">Modifier le profil</a></div>
						<div class="btn-group"><a href="./?op=profile&tab=3" class="btn btn-default <?php if ($tab == 3) echo 'active'; ?>">Modifier l'avatar</a></div>
					</div>
					<br/>
					<?php if ($tab == 1) { ?>
						<div class="panel panel-default">
							<table class="table table-bordered table-striped">
								<tbody>
									<tr>
										<td width="30%"><strong>Date de naissance</strong></td>
										<td><?php $user_datebirthday = new User(); $user_datebirthday->getUserDBUsername($_SESSION['username']); if ($user_datebirthday->getDatebirthday() != "") echo date("d/m/Y", $user_datebirthday->getDatebirthday()); ?></td>
									</tr>
									<tr>
										<td width="30%"><strong>Pays</strong></td>
										<td><?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); echo $user_country->getCountry(); ?></td>
									</tr>
									<tr>
										<td width="30%"><strong>Date d'inscription</strong></td>
										<td><?php $user_dateregistration = new User(); $user_dateregistration->getUserDBUsername($_SESSION['username']); echo date("d/m/Y", $user_dateregistration->getDateregistration()); ?></td>
									</tr>
									<tr>
										<td width="30%"><strong>Date de dernière visite</strong></td>
										<td><?php $user_lastlogin = new User(); $user_lastlogin->getUserDBUsername($_SESSION['username']); echo date("d/m/Y à H:i:s", $user_lastlogin->getDatelastlogin()); ?></td>
									</tr>
									<tr>
										<td width="30%"><strong>Site web</strong></td>
										<td><a href="<?php $user_website = new User(); $user_website->getUserDBUsername($_SESSION['username']); echo $user_website->getUrlwebsite(); ?>" target="_blank"><?php $user_website = new User(); $user_website->getUserDBUsername($_SESSION['username']); echo $user_website->getUrlwebsite(); ?></a></td>
									</tr>
								</tbody>
							</table>
						</div>
					<?php } ?>
					<?php if ($tab == 2) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">Modifier le profil</div>
							<form method="post" class="form-horizontal" action="./?op=profile&tab=2" id="profileEditForm">
								<div class="panel-body">
									<?php
										if (!empty($test[$lib_success]))
										{
											foreach ($test as $type=>$messages)
											{
												foreach ($messages as $message)
												{
													echo "<div class=\"alert alert-success\">".$message."</div>";
												}
											}
										}
									?>
									<input type="hidden" name="profileEditButton" value="1">
									<div class="form-group">
										<label class="col-sm-4 control-label">Identifiant</label>
										<div class="col-sm-8"><input type="text" class="form-control" value="<?php $user_username = new User(); $user_username->getUserDBUsername($_SESSION['username']); echo $user_username->getUsername(); ?>" disabled /></div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Email</label>
										<div class="col-sm-8"><input type="email" class="form-control" name="profile_edit_email" value="<?php $user_email = new User(); $user_email->getUserDBUsername($_SESSION['username']); echo $user_email->getEmail(); ?>" required /></div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Mot de passe</label>
										<div class="col-sm-8"><input type="password" class="form-control" name="profile_edit_password1" autocomplete="off" /></div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Retapez le mot de passe</label>
										<div class="col-sm-8"><input type="password" class="form-control" name="profile_edit_password2" autocomplete="off" /></div>
									</div>
									<hr>
									<div class="form-group">
										<label class="col-sm-4 control-label">Date d'anniversaire</label>
										<div class="col-sm-8"><input type="date" class="form-control datepicker" name="profile_edit_datebirthday" value="<?php $user_datebirthday = new User(); $user_datebirthday->getUserDBUsername($_SESSION['username']); if ($user_datebirthday->getDatebirthday() != "") echo date("d/m/Y", $user_datebirthday->getDatebirthday()); ?>" placeholder="jj/mm/aaaa" /></div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Pays</label>
										<div class="col-sm-8">
											<select class="form-control chosen" name="profile_edit_country">
												<option value=""></option>
												<optgroup label="Europe">
													<option value="Allemagne" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Allemagne") echo "selected"; ?>>Allemagne</option>
													<option value="Albanie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Albanie") echo "selected"; ?>>Albanie</option>
													<option value="Andorre" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Andorre") echo "selected"; ?>>Andorre</option>
													<option value="Autriche" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Autriche") echo "selected"; ?>>Autriche</option>
													<option value="Biélorussie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Biélorussie") echo "selected"; ?>>Biélorussie</option>
													<option value="Belgique" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Belgique") echo "selected"; ?>>Belgique</option>
													<option value="Bosnie-Herzégovine" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Bosnie-Herzégovine") echo "selected"; ?>>Bosnie-Herzégovine</option>
													<option value="Bulgarie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Bulgarie") echo "selected"; ?>>Bulgarie</option>
													<option value="Croatie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Croatie") echo "selected"; ?>>Croatie</option>
													<option value="Danemark" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Danemark") echo "selected"; ?>>Danemark</option>
													<option value="Espagne" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Espagne") echo "selected"; ?>>Espagne</option>
													<option value="Estonie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Estonie") echo "selected"; ?>>Estonie</option>
													<option value="Ex-République Yougoslave de Macédoine" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Ex-République Yougoslave de Macédoine") echo "selected"; ?>>Ex-République Yougoslave de Macédoine</option>
													<option value="Finlande" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Finlande") echo "selected"; ?>>Finlande</option>
													<option value="France" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "France") echo "selected"; ?>>France</option>
													<option value="Grèce" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Grèce") echo "selected"; ?>>Grèce</option>
													<option value="Hongrie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Hongrie") echo "selected"; ?>>Hongrie</option>
													<option value="Irlande" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Irlande") echo "selected"; ?>>Irlande</option>
													<option value="Islande" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Islande") echo "selected"; ?>>Islande</option>
													<option value="Italie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Italie") echo "selected"; ?>>Italie</option>
													<option value="Lettonie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Lettonie") echo "selected"; ?>>Lettonie</option>
													<option value="Liechtenstein" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Liechtenstein") echo "selected"; ?>>Liechtenstein</option>
													<option value="Lituanie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Lituanie") echo "selected"; ?>>Lituanie</option>
													<option value="Luxembourg" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Luxembourg") echo "selected"; ?>>Luxembourg</option>
													<option value="Malte" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Malte") echo "selected"; ?>>Malte</option>
													<option value="Moldavie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Moldavie") echo "selected"; ?>>Moldavie</option>
													<option value="Monaco" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Monaco") echo "selected"; ?>>Monaco</option>
													<option value="Norvège" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Norvège") echo "selected"; ?>>Norvège</option>
													<option value="Pays-Bas" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Pays-Bas") echo "selected"; ?>>Pays-Bas</option>
													<option value="Pologne" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Pologne") echo "selected"; ?>>Pologne</option>
													<option value="Portugal" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Portugal") echo "selected"; ?>>Portugal</option>
													<option value="Roumanie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Roumanie") echo "selected"; ?>>Roumanie</option>
													<option value="Royaume-Uni" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Royaume-Uni") echo "selected"; ?>>Royaume-Uni</option>
													<option value="Russie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Russie") echo "selected"; ?>>Russie</option>
													<option value="Saint-Marin" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Saint-Marin") echo "selected"; ?>>Saint-Marin</option>
													<option value="Serbie-et-Monténégro" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Serbie-et-Monténégro") echo "selected"; ?>>Serbie-et-Monténégro</option>
													<option value="Slovaquie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Slovaquie") echo "selected"; ?>>Slovaquie</option>
													<option value="Slovénie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Slovénie") echo "selected"; ?>>Slovénie</option>
													<option value="Suède" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Suède") echo "selected"; ?>>Suède</option>
													<option value="Suisse" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Suisse") echo "selected"; ?>>Suisse</option>
													<option value="République Tchèque" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "République Tchèque") echo "selected"; ?>>République Tchèque</option>
													<option value="Ukraine" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Ukraine") echo "selected"; ?>>Ukraine</option>
													<option value="Vatican" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Vatican") echo "selected"; ?>>Vatican</option>
												</optgroup>
												<optgroup label="Afrique">
													<option value="Afrique du Sud" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Afrique du Sud") echo "selected"; ?>>Afrique du Sud</option>
													<option value="Algérie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Algérie") echo "selected"; ?>>Algérie</option>
													<option value="Angola" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Angola") echo "selected"; ?>>Angola</option>
													<option value="Bénin" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Bénin") echo "selected"; ?>>Bénin</option>
													<option value="Botswana" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Botswana") echo "selected"; ?>>Botswana</option>
													<option value="Burkina" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Burkina") echo "selected"; ?>>Burkina</option>
													<option value="Burundi" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Burundi") echo "selected"; ?>>Burundi</option>
													<option value="Cameroun" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Cameroun") echo "selected"; ?>>Cameroun</option>
													<option value="Cap-Vert" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Cap-Vert") echo "selected"; ?>>Cap-Vert</option>
													<option value="République Centre-Africaine" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "République Centre-Africaine") echo "selected"; ?>>République Centre-Africaine</option>
													<option value="Comores" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Comores") echo "selected"; ?>>Comores</option>
													<option value="République Démocratique du Congo" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "République Démocratique du Congo") echo "selected"; ?>>République Démocratique du Congo</option>
													<option value="Congo" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Congo") echo "selected"; ?>>Congo</option>
													<option value="Côte d'Ivoire" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Côte d\'Ivoire") echo "selected"; ?>>Côte d'Ivoire</option>
													<option value="Djibouti" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Djibouti") echo "selected"; ?>>Djibouti</option>
													<option value="Égypte" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Égypte") echo "selected"; ?>>Égypte</option>
													<option value="Éthiopie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Éthiopie") echo "selected"; ?>>Éthiopie</option>
													<option value="Érythrée" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Érythrée") echo "selected"; ?>>Érythrée</option>
													<option value="Gabon" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Gabon") echo "selected"; ?>>Gabon</option>
													<option value="Gambie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Gambie") echo "selected"; ?>>Gambie</option>
													<option value="Ghana" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Ghana") echo "selected"; ?>>Ghana</option>
													<option value="Guinée" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Guinée") echo "selected"; ?>>Guinée</option>
													<option value="Guinée-Bisseau" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Guinée-Bisseau") echo "selected"; ?>>Guinée-Bisseau</option>
													<option value="Guinée Équatoriale" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Guinée Équatoriale") echo "selected"; ?>>Guinée Équatoriale</option>
													<option value="Kenya" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Kenya") echo "selected"; ?>>Kenya</option>
													<option value="Lesotho" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Lesotho") echo "selected"; ?>>Lesotho</option>
													<option value="Liberia" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Liberia") echo "selected"; ?>>Liberia</option>
													<option value="Libye" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Libye") echo "selected"; ?>>Libye</option>
													<option value="Madagascar" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Madagascar") echo "selected"; ?>>Madagascar</option>
													<option value="Malawi" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Malawi") echo "selected"; ?>>Malawi</option>
													<option value="Mali" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Mali") echo "selected"; ?>>Mali</option>
													<option value="Maroc" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Maroc") echo "selected"; ?>>Maroc</option>
													<option value="Maurice" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Maurice") echo "selected"; ?>>Maurice</option>
													<option value="Mauritanie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Mauritanie") echo "selected"; ?>>Mauritanie</option>
													<option value="Mozambique" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Mozambique") echo "selected"; ?>>Mozambique</option>
													<option value="Namibie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Namibie") echo "selected"; ?>>Namibie</option>
													<option value="Niger" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Niger") echo "selected"; ?>>Niger</option>
													<option value="Nigéria" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Nigéria") echo "selected"; ?>>Nigéria</option>
													<option value="Ouganda" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Ouganda") echo "selected"; ?>>Ouganda</option>
													<option value="Rwanda" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Rwanda") echo "selected"; ?>>Rwanda</option>
													<option value="Sao Tomé-et-Principe" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Sao Tomé-et-Principe") echo "selected"; ?>>Sao Tomé-et-Principe</option>
													<option value="Sénégal" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Sénégal") echo "selected"; ?>>Sénégal</option>
													<option value="Seychelles" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Seychelles") echo "selected"; ?>>Seychelles</option>
													<option value="Sierra" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Sierra") echo "selected"; ?>>Sierra</option>
													<option value="Somalie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Somalie") echo "selected"; ?>>Somalie</option>
													<option value="Soudan" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Soudan") echo "selected"; ?>>Soudan</option>
													<option value="Swaziland" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Swaziland") echo "selected"; ?>>Swaziland</option>
													<option value="Tanzanie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Tanzanie") echo "selected"; ?>>Tanzanie</option>
													<option value="Tchad" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Tchad") echo "selected"; ?>>Tchad</option>
													<option value="Togo" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Togo") echo "selected"; ?>>Togo</option>
													<option value="Tunisie <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Tunisie") echo "selected"; ?>">Tunisie</option>
													<option value="Zambie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Zambie") echo "selected"; ?>>Zambie</option>
													<option value="Zimbabwe" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Zimbabwe") echo "selected"; ?>>Zimbabwe</option>
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
													<option value="Australie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Australie") echo "selected"; ?>>Australie</option>
													<option value="Fidji" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Fidji") echo "selected"; ?>>Fidji</option>
													<option value="Kiribati" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Kiribati") echo "selected"; ?>>Kiribati</option>
													<option value="Marshall" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Marshall") echo "selected"; ?>>Marshall</option>
													<option value="Micronésie" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Micronésie") echo "selected"; ?>>Micronésie</option>
													<option value="Nauru" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Nauru") echo "selected"; ?>>Nauru</option>
													<option value="Nouvelle-Zélande" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Nouvelle-Zélande") echo "selected"; ?>>Nouvelle-Zélande</option>
													<option value="Palaos" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Palaos") echo "selected"; ?>>Palaos</option>
													<option value="Papouasie-Nouvelle-Guinée" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Papouasie-Nouvelle-Guinée") echo "selected"; ?>>Papouasie-Nouvelle-Guinée</option>
													<option value="Salomon" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Salomon") echo "selected"; ?>>Salomon</option>
													<option value="Samoa" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Samoa") echo "selected"; ?>>Samoa</option>
													<option value="Tonga" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Tonga") echo "selected"; ?>>Tonga</option>
													<option value="Tuvalu" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Tuvalu") echo "selected"; ?>>Tuvalu</option>
													<option value="Vanuatu" <?php $user_country = new User(); $user_country->getUserDBUsername($_SESSION['username']); if ($user_country->getCountry() == "Vanuatu") echo "selected"; ?>>Vanuatu</option>
												</optgroup>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Site web</label>
										<div class="col-sm-8"><input type="url" class="form-control" name="profile_edit_urlwebsite" value="<?php $user_website = new User(); $user_website->getUserDBUsername($_SESSION['username']); echo $user_website->getUrlwebsite(); ?>" placeholder="http://" /></div>
									</div>
								</div>
								<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Modifier</button></div>
							</form>
						</div>
					<?php } ?>
					<?php if ($tab == 3) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">Modifier l'avatar</div>
							<form method="post" action="./?op=profile&tab=3" enctype="multipart/form-data">
								<input type="hidden" name="avatarEditButton" value="1">
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
										if (!empty($test[$lib_success]))
										{
											foreach ($test as $type=>$messages)
											{
												foreach ($messages as $message)
												{
													echo "<div class=\"alert alert-success\">".$message."</div>";
												}
											}
										}
									?>
									<h4>Votre avatar</h4>
									<div class="form-group">
										<div class="row">
											<div class="col-xs-12 col-sm-4 col-md-3">
												<label>
													<small class="help-block">
														<?php
															$setting_avatar_width = new Setting();
															$setting_avatar_width->getSettingDBKey('avatar_width');
															
															$setting_avatar_height = new Setting();
															$setting_avatar_height->getSettingDBKey('avatar_height');
															
															$setting_avatar_weight = new Setting();
															$setting_avatar_weight->getSettingDBKey('avatar_weight');
															
															echo "Le fichier doit être au format <strong>".$avatar_extension."</strong>, de taille <strong>".$setting_avatar_width->getValue()."x".$setting_avatar_height->getValue()."</strong> et avoir un poids de <strong>".($setting_avatar_weight->getValue() / 1024)." ko</strong> maximum.";
														?>
													</small>
												</label>
											</div>
											<div class="col-xs-12 col-sm-8 col-md-9">
												<div class="fileinput fileinput-new" data-provides="fileinput">
													<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width:100%;height:170px;"></div>
													<div>
														<span class="btn btn-default btn-file">
															<span class="fileinput-new">Sélectionner une image</span>
															<span class="fileinput-exists">Modifier</span>
															<input type="file" name="avatar_edit" />
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
													$user_avatar = new User();
													$user_avatar->getUserDBUsername($_SESSION['username']);
													if ($user_avatar->getAvatar() == $i.".png") $checked = "checked"; else $checked = "";
													echo "<label class=\"avatar-theme\"><input type=\"radio\" name=\"avatar_edit\" value=\"".$i.".png\" ".$checked." /><img src=\"img/avatars/".$i.".png\" style=\"width:114px;\" title=\"Avatar\" /></label>";
												}
											?>
										</div>
									</div>
								</div>
								<div class="panel-footer clearfix"><button type="submit" class="btn btn-success pull-right">Modifier</button></div>
							</form>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php include('./template/bootstrap/includes/footer.php'); ?>
		<?php include('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>