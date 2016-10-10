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

	if (isset($_['step2']) && $_['step2'] == 1 && empty($test[$lib_errors]))
	{
		if (!empty($_['category_name']))
		{
			$category_name = $_['category_name'];

			$category = new Category();
			$category->setName($category_name);
			$category->saveCategory();

			$category_presence = $category->testPresenceCategory($category_name);
			if ($category_presence)
			{
				header("location: ./step3.php");
				exit();
			} else {
				$test[$lib_errors][] = "Impossible d'ajouter la catégorie.";
			}
		} else {
			$test[$lib_errors][] = "Il est nécessaire de fournir un nom pour la catégorie.";
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Etape 2 : Ajout d'une catégorie</title>
		<!-- BOOTSTRAP 3.3.7 -->
		<link rel="stylesheet" href="../template/bootstrap/css/bootstrap.min.css">
		<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
		<link rel="stylesheet" href="../template/bootstrap/plugins/bootstrap-validator/css/bootstrap-validator.min.css">
		<!-- FONT-AWESOME 4.6.3 -->
		<link rel="stylesheet" href="../template/bootstrap/plugins/font-awesome/css/font-awesome.min.css" />
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
							<p>Cette étape, qui n'est pas obligatoire et peut être effectuée plus tard, permet de créer votre première catégorie et permettra l'accès aux différents menus.</p><br/>
							<p>Exemple ci-dessous, le nom de la catégorie est "Collections" :</p>
							<p class="text-center"><img src="./img/step2.jpg" /></p>
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
							<h4 class="panel-title pull-left" style="padding-top: 7.5px;">ETAPE 2 : AJOUT D'UNE CATEGORIE</h4>
							<div class="pull-right"><button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-question"></i></button></div>
						</div>
						<form method="post" action="./step2.php" id="step2Form">
							<input type="hidden" name="step2" value="1" />
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
								<div class="form-group has-feedback">
									<input type="text" class="form-control" name="category_name" placeholder="Nom de la catégorie" autofocus required />
									<span class="form-control-feedback"><i class="fa fa-archive"></i></span>
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
		<script>
			$("#step2Form").bootstrapValidator(
			{
				locale: "fr_FR",
				fields:
				{
					category_name:
					{
						validators:
						{
							notEmpty:
							{
							},
							stringLength:
							{
								min: 4,
								max: 30
							}
						}
					}
				}
			});
		</script>
	</body>
</html>