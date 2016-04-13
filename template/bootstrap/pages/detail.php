<?php
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach($_ as $key=>&$val)
	{
		Functions::secure($val);
	}
	
	$menu_query = $db->prepare('SELECT `id`, `table`, `type` FROM site_menu WHERE `id` = "'.$table.'"');
	$menu_query->execute();
	$menu = $menu_query->fetch();
	$menu_query->closeCursor();
	
	$query = $db->prepare('SELECT * FROM '.$menu['table'].' WHERE `ID` = '.$id);
	$query->execute();
	$detail = $query->fetch();
	$query->closeCursor();
?>
<script>document.title += ' - Détail'</script>
<?php echo $menu['table']; ?>
<br/>
<?php echo $id; ?>
<br/>
<?php echo $detail['TitreVF']; ?>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8 text-center">
		<?php $filename = sprintf('./profils/'.$menu['table'].'/affiches/Filmotech_%05d.jpg', $detail['ID']); ?>
		<?php if (file_exists($filename)) echo '<div class="detail"><img data-original="'.$filename.'" class="detail-img lazy" alt="affiche" /></div>'; else echo '<div class="detail"><img data-src="holder.js/100px165?text=aucune \n image" alt="affiche" /></div>'; ?>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-4">
	</div>
</div>