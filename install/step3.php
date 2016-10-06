<?php
	include("../class/_classLoader.php");

	$install_terminee = false;
	$lib_errors = "Erreurs";

	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key => &$val)
	{
		Functions::secure($val);
	}

	if (isset($_POST['step3']) && $_POST['step3'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_['menu_category']) && !empty($_['menu_name']) && !empty($_['menu_icon']) && !empty($_['menu_table']))
		{
			$menu_category = $_['menu_category'];
			$menu_name = $_['menu_name'];
			$menu_icon = $_['menu_icon'];
			$menu_table = $_['menu_table'];

			$menu = new Menu();
			$menu->setName($menu_name);
			$menu->setIcon($menu_icon);
			$menu->setPosition(0);
			$menu->setNametable($menu_table);
			$menu->setIDcategory($menu_category);
			$menu->saveMenu();

			$menu_presence = $menu->testPresenceMenu($menu_name);
			if ($menu_presence)
			{
				header("location: ./end.php");
				exit();
			} else {
				$test[$lib_errors][] = "Impossible d'ajouter le menu.";
			}
		} else {
			$test[$lib_errors][] = "Il est nécessaire de fournir le nom et l'icone du menu, ainsi que le nom de la table.";
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Etape 3 : Ajout d'un menu</title>
		<!-- BOOTSTRAP 3.3.7 -->
		<link rel="stylesheet" href="../template/bootstrap/css/bootstrap.min.css">
		<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
		<link rel="stylesheet" href="../template/bootstrap/plugins/bootstrap-validator/css/bootstrap-validator.min.css">
		<!-- FONT-AWESOME 4.6.3 -->
		<link rel="stylesheet" href="../template/bootstrap/plugins/font-awesome/css/font-awesome.min.css" />
		<!-- SELECT2 4.0.3 -->
		<link rel="stylesheet" href="../template/bootstrap/plugins/select2/css/select2.min.css">
		<link rel="stylesheet" href="../template/bootstrap/plugins/select2/css/select2-bootstrap.min.css">
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<style>
			body
			{
				padding-top: 100px;
				background-color: #eee;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<!-- INFORMATIONS -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Informations</h4>
						</div>
						<div class="modal-body">
							<p>Cette étape, qui n'est pas obligatoire et peut être effectuée plus tard, permet de créer votre premier menu et permettra l'accès aux contenus de votre table.</p><br/>
							<p>Exemple ci-dessous, le nom du menu est "Vidéo"</p>
							<p class="text-center"><img src="./img/step3-1.jpg" /></p><br/>
							<p>En validant cette étape, un dossier sera créé dans "profils" et portera le nom de la table.</p>
							<p class="text-center"><img src="./img/step3-2.jpg" /></p><br/>
							<p>Une fois le menu créé, voici ce qu'il faudra faire avec filmotech, que ce soit avec un site héberger en local ou à distance.</p>
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">Hébergement local</a></li>
								<li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Hébergeurs</a></li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane active" id="tab1">
									<p>Dans la section "Adresse du serveur de publication", vous devez indiquer l'url de votre site</p>
									<p class="text-center"><img src="./img/step3-3.jpg" /></p>
									<p>Dans la section "Gestion des affiches", le répertoire des affiches doit se nommer obligatoirement affiches</p>
									<p class="text-center"><img src="./img/step3-4.jpg" /></p>
									<p>Dans la section "Accès à la base de données distante", remplir les champs comme à l'étape 1 et 3</p>
									<p class="text-center"><img src="./img/step3-5.jpg" /></p>
									<p>Dans la section "Site Web Filmotech", décocher la case Inclure le site Web Filmotech et cliquer sur le bouton Générer les fichiers</p>
									<p class="text-center"><img src="./img/step3-6.jpg" /></p>
									<p>Sélectionner le dossier qui se trouve dans "Profils" et cliquer sur le bouton ok</p>
									<p class="text-center"><img src="./img/step3-7.jpg"/></p>
									<p>Si tout est ok, il ne reste plus qu'a cliquer sur le bouton publier</p>
									<p class="text-center"><img src="./img/step3-8.jpg" /></p><br/>
								</div>
								<div class="tab-pane" id="tab2">
									<p>A faire.</p>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
						</div>
					</div>
				</div>
			</div>
			<!-- FORMULAIRE -->
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
					<div class="panel panel-default">
						<div class="panel-heading clearfix">
							<h4 class="panel-title pull-left" style="padding-top: 7.5px;">ETAPE 3 : AJOUT D'UN MENU</h4>
							<div class="pull-right"><button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-question"></i></button></div>
						</div>
						<form method="post" action="./step3.php" id="step3Form">
							<input type="hidden" name="step3" value="1" />
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
								<h4>Catégorie</h4>
								<div class="form-group">
									<select class="form-control select2" name="menu_category" style="width:100%;" autofocus required>
										<option></option>
										<?php
											$category = new Category();
											$liste_category = $category->getCategoryList();
											foreach ($liste_category as $category => $val_category)
											{
												echo "<option value=\"".$val_category['id']."\">".$val_category['name']."</option>";
											}
										?>
									</select>
								</div>
								<hr>
								<h4>Menu</h4>
								<div class="form-group has-feedback">
									<input type="text" class="form-control" name="menu_name" placeholder="Nom du menu" required />
									<span class="form-control-feedback"><i class="fa fa-pencil"></i></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<input type="text" class="form-control" name="menu_icon" id="menuIcon" placeholder="Nom de l'icône" required />
										<div class="input-group-btn">
											<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><i class="fa fa-info"></i></button>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='align-justify'"><i class="fa fa-align-justify"></i> align-justify</a></li>
												<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='book'"><i class="fa fa-book"></i> book</a></li>
												<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='film'"><i class="fa fa-film"></i> film</a></li>
												<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='gamepad'"><i class="fa fa-gamepad"></i> gamepad</a></li>
												<li><a href="#menuIcon" onclick="document.getElementById('menuIcon').value='music'"><i class="fa fa-music"></i> music</a></li>
											</ul>
										</div>
									</div>
								</div>
								<div class="form-group has-feedback">
									<input type="text" class="form-control" name="menu_table" placeholder="Nom de la table" required />
									<span class="form-control-feedback"><i class="fa fa-table"></i></span>
								</div>
							</div>
							<div class="panel-footer clearfix">
								<a href="./end.php" class="btn btn-primary">Passer cette étape</a>
								<button type="submit" class="btn btn-success pull-right">Etape suivante</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- JQUERY 3.1.1 -->
		<script src="../template/bootstrap/js/jquery.min.js"></script>
		<!-- BOOTSTRAP 3.3.7 -->
		<script src="../template/bootstrap/js/bootstrap.min.js"></script>
		<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
		<script src="../template/bootstrap/plugins/bootstrap-validator/js/bootstrap-validator.min.js"></script>
		<script src="../template/bootstrap/plugins/bootstrap-validator/js/i18n/fr_FR.js"></script>
		<!-- SELECT2 4.0.3 -->
		<script src="../template/bootstrap/plugins/select2/js/select2.full.min.js"></script>
		<script src="../template/bootstrap/plugins/select2/js/i18n/fr.js"></script>
		<script>
			$("#myTabs a").click(function (e)
			{
				e.preventDefault()
				$(this).tab("show")
			})

			// Bootstrap Validator
			$("#step3Form").bootstrapValidator(
			{
				locale: "fr_FR",
				fields:
				{
					menu_category:
					{
						validators:
						{
							notEmpty:
							{
							}
						}
					},
					menu_name:
					{
						validators:
						{
							notEmpty:
							{
							}
						}
					},
					menu_table:
					{
						validators:
						{
							notEmpty:
							{
							}
						}
					},
					menu_icon:
					{
						validators:
						{
							notEmpty:
							{
							}
						}
					}
				}
			});

			// Select2
			$(".select2").select2(
			{
				minimumResultsForSearch: Infinity,
				placeholder: "Choisir la catégorie",
				theme: "bootstrap",
				language: "fr"
			});
		</script>
	</body>
</html>