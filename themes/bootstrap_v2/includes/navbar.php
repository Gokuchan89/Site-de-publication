<?php
	// Valeurs par défaut, remplacées si une autre valeur est saisie
	foreach (array('loginUsername', 'loginPassword') as $var)
	{
		if (!empty($_[$var]))
		{
			$$var = $_[$var];
		} else {
			$$var = '';
		}
	}

	if (isset($_['loginButton']))
	{
		// Vérification si tous les champs sont renseignés
		if (empty($_['loginUsername']) || empty($_['loginPassword']))
		{
			$loginMessage = 'Veuillez renseigner un nom d\'utilisateur et/ou un mot de passe.';
			$i++;
		}

		require_once('./includes/mysqlConstants.php');
		require_once('./includes/mysqlConnector.php');

		$query = $db->prepare('SELECT `username`, `password`, `access` FROM `site_user` WHERE `username` = :username');
		$query->bindValue(':username', $_['loginUsername'], PDO::PARAM_STR);
		$query->execute();
		$loginVerif = $query->fetch();
		$query->CloseCursor();

		// Vérification si le nom d'utilisateur est présent dans la table site_user et si le mot de passe est valide
		if (($loginVerif['username'] != $_['loginUsername'] || $loginVerif['password'] != md5($_['loginPassword'])) && $i == 0)
		{
			$loginMessage = 'Nom d\'utilisateur et/ou mot de passe invalide.';
			$i++;
		}

		// Vérification si l'utilisateur a accès au site
		if ($loginVerif['access'] == 0 && $i == 0)
		{
			$loginMessage = 'Votre compte n\'est pas encore activé.';
			$i++;
		}
	}

	// Pas d'erreur, l'utilisateur se connecte
	if (isset($_['loginButton']) && $i == 0)
	{
		$_SESSION['username'] = $_['loginUsername'];
		header('location: ./');
		exit();
	}
?>
<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="<?php echo $config['root']; ?>" class="navbar-brand"><?php echo $config['title']; ?></a>
		</div>
		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<?php if ($op && $op == 'detail') echo '<li><a href="javascript:history.back();"><i class="fa fa-arrow-left"></i></a></li>'; ?>
				<li <?php if (!$op) echo 'class="active"'; ?>><a href="<?php echo $config['root']; ?>">Accueil</a></li>
				<?php while ($category = $category_query->fetch()) { ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $category['name']; ?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php
								$menu_query = $db->prepare('SELECT `id`, `name`, `icon`, `category`, `table`, `type` FROM `site_menu` WHERE `category` = :category ORDER BY `position`');
								$menu_query->bindValue(':category', $category['id'], PDO::PARAM_STR);
								$menu_query->execute();
							?>
							<?php while ($menu = $menu_query->fetch()) { ?>
								<?php if ($op == 'list' && $table == $menu['id']) $active = 'class="active"'; else $active = ''; ?>
								<li <?php echo $active; ?>><a href="./?op=list&table=<?php echo $menu['id']; ?>"><i class="fa fa-<?php echo $menu['icon']; ?>"></i> <?php echo $menu['name']; ?></a></li>
							<?php } $menu_query->closeCursor(); ?>
						</ul>
					</li>
				<?php } $category_query->closeCursor(); ?>
			</ul>
			<?php if ($config['open'] == '1' && !isset($_SESSION['username'])) { ?>
				<form method="POST" class="navbar-form navbar-right">
					<div class="form-group <?php if (isset($loginMessage)) echo 'has-error'; ?>"><input type="text" class="form-control" name="loginUsername" value="<?php echo $loginUsername; ?>" placeholder="Nom d'utilisateur" /></div>
					<div class="form-group <?php if (isset($loginMessage)) echo 'has-error'; ?>"><input type="password" class="form-control" name="loginPassword" value="<?php echo $loginPassword; ?>" placeholder="Mot de passe" /></div>
					<button type="submit" class="btn btn-success" name="loginButton">Se connecter</button>
				</form>
			<?php } ?>
			<?php if (isset($_SESSION['username'])) { ?>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user['username']; ?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li <?php if ($op == 'profile') echo 'class="active"'; ?>><a href="./?op=profile">Profil</a></li>
							<li <?php if ($op == 'themes') echo 'class="active"'; ?>><a href="./?op=themes">Thèmes</a></li>
							<?php if ($user['rank'] == '3') { ?><li <?php if ($op == 'settings') echo 'class="active"'; ?>><a href="./?op=settings">Paramètres</a></li><?php } ?>
							<li class="divider" role="separator"></li>
							<li><a href="./?op=logout">Déconnexion</a></li>
						</ul>
					</li>
				</ul>
			<?php } ?>
		</div>
	</div>
</nav>