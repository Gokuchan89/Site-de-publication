<?php
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach($_ as $key=>&$val)
	{
		Functions::secure($val);
	}

	// Valeurs par défaut, remplacées si une autre valeur est saisie
	foreach (array('username', 'password') as $var)
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
	
	// On vérifie que tous les champs sont renseignés et correct
	if(isset($_['loginButton']))
	{
		if (empty($_['username']) || empty($_['password']))
		{
			$loginMessage[$lib_errors][] = 'Il est nécessaire de fournir un nom d\'utilisateur et un mot de passe.';
		}
		
		$query = $db->prepare('SELECT `username`, `password`, `access` FROM site_user WHERE `username` = :username');
		$query->bindValue(':username', $_['username'], PDO::PARAM_STR);
		$query->execute();
		$login_verif = $query->fetch();
		$query->CloseCursor();

		if ($login_verif['username'] != $_['username'] && empty($loginMessage[$lib_errors]))
		{
			$loginMessage[$lib_errors][] = 'Ce compte n\'existe pas.';
		}
		if ($login_verif['password'] != md5($password) && empty($loginMessage[$lib_errors]))
		{
			$loginMessage[$lib_errors][] = 'Ce mot de passe est incorrect.';
		}
		if ($login_verif['access'] == 0 && empty($loginMessage[$lib_errors]))
		{
			$loginMessage[$lib_errors][] = 'Ce compte n\'est pas encore activé.';
		}
	}
	
	if(isset($_['loginButton']) && empty($loginMessage[$lib_errors]))
	{
		$_SESSION['username'] = $_['username'];
		
		refresh($_SERVER['REQUEST_URI']);
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
		<div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<?php if($op && $op == 'detail') echo '<li><a href="javascript:history.back();"><i class="fa fa-arrow-left"></i></a></li>'; ?>
				<li <?php if(!$op) echo 'class="active"'; ?>><a href="<?php echo $config['root']; ?>">Accueil</a></li>
				<?php
					while($category = $category_query->fetch())
					{
						echo '<li class="dropdown">';
						echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.$category['name'].' <span class="caret"></span></a>';
						echo '<ul class="dropdown-menu">';
						$menu_query = $db->prepare('SELECT `id`, `name`, `icon`, `category`, `table`, `type` FROM site_menu WHERE `category` = "'.$category['id'].'" ORDER BY `position`');
						$menu_query->execute();
						while($menu = $menu_query->fetch())
						{
							if($op == 'list' && $table == $menu['id']) $active = 'class="active"'; else $active = '';
							echo '<li '.$active.'><a href="./?op=list&table='.$menu['id'].'"><i class="fa fa-'.$menu['icon'].'"></i> '.$menu['name'].'</a></li>';
						} $menu_query->closeCursor();
						echo '</ul>';
						echo '</li>';
					} $category_query->closeCursor();
				?>
			</ul>
			<?php if($config['open'] == '0') { ?>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user['username']; ?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li <?php if($op == 'profile') echo 'class="active"'; ?>><a href="./?op=profile">Profil</a></li>
							<li <?php if($op == 'themes') echo 'class="active"'; ?>><a href="./?op=themes">Thèmes</a></li>
							<?php if($user['rank'] == '3') { ?><li <?php if($op == 'settings') echo 'class="active"'; ?>><a href="./?op=settings">Paramètres</a></li><?php } ?>
							<li role="separator" class="divider"></li>
							<li><a href="./?op=logout">Déconnexion</a></li>
						</ul>
					</li>
				</ul>
			<?php } else { ?>
				<?php if(!isset($_SESSION['username'])) { ?>
					<div id="navbar" class="navbar-collapse collapse">
						<form method="POST" class="navbar-form navbar-right">
							<div class="form-group">
								<input type="text" class="form-control" name="username" value="<?php echo $username; ?>" placeholder="Nom d'utilisateur" />
							</div>
							<div class="form-group">
								<input type="password" class="form-control" name="password" value="<?php echo $password; ?>" placeholder="Mot de passe" />
							</div>
							<button type="submit" class="btn btn-success" name="loginButton">Se connecter</button>
						</form>
					</div>
				<?php } else { ?>
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user['username']; ?> <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li <?php if($op == 'profile') echo 'class="active"'; ?>><a href="./?op=profile">Profil</a></li>
								<li <?php if($op == 'themes') echo 'class="active"'; ?>><a href="./?op=themes">Thèmes</a></li>
								<?php if($user['rank'] == '3') { ?><li <?php if($op == 'settings') echo 'class="active"'; ?>><a href="./?op=settings">Paramètres</a></li><?php } ?>
								<li role="separator" class="divider"></li>
								<li><a href="./?op=logout">Déconnexion</a></li>
							</ul>
						</li>
					</ul>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</nav>