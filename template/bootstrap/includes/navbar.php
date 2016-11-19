<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="./" class="navbar-brand"><?php $setting_title = new Setting(); $setting_title->getSettingDBKey('title'); echo $setting_title->getValue(); ?></a>
		</div>
		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<?php if ($op && $op == 'detail') { ?><li><a href="javascript:history.back();"><i class="fa fa-arrow-left"></i></a></li><?php } ?>
				<li <?php if (!$op) echo 'class="active"'; ?>><a href="./">Accueil</a></li>
				<?php
					$category = new Category();
					$liste_category = $category->getCategoryList();
				?>
				<?php foreach ($liste_category as $category => $val_category) { ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $val_category['name']; ?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li <?php if ($op == "lastupdate" && $_GET['category'] == $val_category['id']) echo "class=\"active\""; ?>><a href="./?op=lastupdate&category=<?php echo $val_category['id']; ?>"><i class="fa fa-dashboard"></i> Derniers ajouts</a></li>
							<?php
								$menu_list = new Menu();
								$menu_list = $menu_list->getMenuDBIDCategory($val_category['id']);
							?>
							<?php foreach ($menu_list as $menu => $val_menu) { ?>
								<li <?php if ($op == "list" && $_GET['menu'] == $val_menu['id']) echo "class=\"active\""; ?>><a href="./?op=list&category=<?php echo $val_category['id']; ?>&menu=<?php echo $val_menu['id']; ?>"><i class="fa fa-<?php echo $val_menu['icon']; ?>"></i> <?php echo $val_menu['name']; ?></a></li>
							<?php } ?>
						</ul>
					</li>
				<?php } ?>
			</ul>
			<?php
				$setting_open = new Setting();
				$setting_open->getSettingDBKey('open');
				if ($setting_open->getValue() == 1 && !isset($_SESSION['username']))
				{
			?>
				<div class="navbar-form navbar-right">
					<?php
						$setting_registration = new Setting();
						$setting_registration->getSettingDBKey('registration');
						if ($setting_registration->getValue() == 1)
						{
							echo "<a href=\"register.php\" class=\"btn btn-primary\">S'inscrire</a>";
						}
					?>
					<a href="login.php" class="btn btn-success">Se connecter</a>
				</div>
			<?php } ?>
			<?php if (isset($_SESSION['username'])) { ?>
				<ul class="nav navbar-nav navbar-right">
					<li <?php if ($op == "search") echo 'class="active"'; ?>><a href="./?op=search"><i class="fa fa-search"></i></a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['name']; ?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php if ($_SESSION['username'] == 'anonymous') { ?>
								<li class="disabled"><a href="#">Profil</a></li>
								<li class="disabled"><a href="#">Thèmes</a></li>
							<?php } ?>
							<?php if ($_SESSION['username'] != 'anonymous') { ?>
								<li <?php if ($op == 'profile') echo 'class="active"'; ?>><a href="./?op=profile">Profil</a></li>
								<li <?php if ($op == 'themes') echo 'class="active"'; ?>><a href="./?op=themes">Thèmes</a></li>
							<?php } ?>
							<?php if ($_SESSION['admin'] == 1) { ?>
								<li class="divider" role="separator"></li>
								<li <?php if ($op == 'settings') echo 'class="active"'; ?>><a href="?op=settings">Paramètres</a></li>
								<li <?php if ($op == 'users') echo 'class="active"'; ?>><a href="?op=users">Utilisateurs</a></li>
								<li <?php if ($op == 'modules') echo 'class="active"'; ?>><a href="?op=modules">Modules</a></li>
								<li <?php if ($op == 'log') echo 'class="active"'; ?>><a href="?op=log">Historique d'activité</a></li>
							<?php } ?>
							<li class="divider" role="separator"></li>
							<li><a href="?op=logout">Déconnexion</a></li>
						</ul>
					</li>
				</ul>
			<?php } ?>
		</div>
	</div>
</nav>