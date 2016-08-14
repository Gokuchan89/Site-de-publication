<?php
	/*
		=================================
		CATEGORIES
		=================================
	*/
	$category_query = $db->prepare('SELECT `id`, `name` FROM `site_category`');
	$category_query->execute();
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
				<div class="navbar-form navbar-right">
					<a href="register.php" class="btn btn-primary">S'inscrire</a>
					<a href="login.php" class="btn btn-success">Se connecter</a>
				</div>
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
