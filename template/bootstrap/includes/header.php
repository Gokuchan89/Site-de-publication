<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php $setting_title = new Setting(); $setting_title->getSettingDBKey('title'); echo $setting_title->getValue(); ?></title>
	<!-- BOOTSTRAP 3.3.7 -->
	<link rel="stylesheet" href="./template/bootstrap/css/bootstrap.min.css">
	<!-- FONT-AWESOME 4.6.3 -->
	<link rel="stylesheet" href="./template/bootstrap/plugins/font-awesome/css/font-awesome.min.css" />
	
	
	
	
	
	
	<!-- Page paramètres -->
	<?php if ($op == 'settings') { ?>
		<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
		<link rel="stylesheet" href="./template/bootstrap/plugins/bootstrap-validator/css/bootstrap-validator.min.css">
		<!-- BOOTSTRAP WYSIHTML5 -->
		<link rel="stylesheet" href="./template/bootstrap/plugins/bootstrap-wysihtml5/css/bootstrap3-wysihtml5.min.css">
		<!-- CHOSEN -->
		<link rel="stylesheet" href="./template/bootstrap/plugins/chosen/css/chosen-bootstrap.css">
	<?php } ?>
	
	
	
	
	
	<!-- Page utilisateurs -->
	<?php if ($op == 'users') { ?>
		<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
		<link rel="stylesheet" href="./template/bootstrap/plugins/bootstrap-validator/css/bootstrap-validator.min.css">
		<!-- CHOSEN -->
		<link rel="stylesheet" href="./template/bootstrap/plugins/chosen/css/chosen-bootstrap.css">
		<!-- DATATABLES 1.10.12 -->
		<link rel="stylesheet" href="./template/bootstrap/plugins/datatables/css/datatables.bootstrap.min.css">
		<link rel="stylesheet" href="./template/bootstrap/plugins/datatables/css/datatables.fontawesome.css">
	<?php } ?>
	
	
	
	
	
	<!-- Page historique d'activité -->
	<?php if ($op == 'log') { ?>
		<!-- DATATABLES 1.10.12 -->
		<link rel="stylesheet" href="./template/bootstrap/plugins/datatables/css/datatables.bootstrap.min.css">
		<link rel="stylesheet" href="./template/bootstrap/plugins/datatables/css/datatables.fontawesome.css">
	<?php } ?>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	<!-- SITE -->
	<link rel="stylesheet" href="./template/bootstrap/plugins/site/css/site.css">
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>