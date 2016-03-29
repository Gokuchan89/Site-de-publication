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
</div>

<?php echo $profile['id']; ?>