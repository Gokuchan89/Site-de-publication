<!-- JQUERY 2.2.3 -->
<script src="./template/bootstrap/js/jquery.min.js"></script>
<!-- BOOTSTRAP 3.3.7 -->
<script src="./template/bootstrap/js/bootstrap.min.js"></script>
<!-- JQUERY UI 1.11.4 -->
<script src="./template/bootstrap/plugins/jquery-ui/js/jquery-ui.min.js"></script>
<!-- LAZYLOAD 1.9.7 -->
<script src="./template/bootstrap/plugins/lazyload/js/lazyload.min.js"></script>
<!-- LIGHTGALLERY 1.2.18 -->
<script src="./template/bootstrap/plugins/lightgallery/js/lightgallery.js"></script>
<!-- LIGHTGALLERY LG-VIDEO 1.2.18 -->
<script src="./template/bootstrap/plugins/lightgallery/js/lg-video.js"></script>
<!-- SELECT2 4.0.3 -->
<script src="./template/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<!-- SELECT2 LANG 4.0.3 -->
<script src="./template/bootstrap/plugins/select2/js/i18n/fr.js"></script>
<!-- HOLDER 2.9.0 -->
<script src="./template/bootstrap/plugins/holder/js/holder.min.js"></script>
<!-- JASNY BOOTSTRAP 3.1.3 -->
<script src="./template/bootstrap/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js"></script>
<!-- BXSLIDER 4.2.5 -->
<script src="./template/bootstrap/plugins/bxslider/js/bxslider.min.js"></script>
<script>
	$(document).ready( function()
	{
		// Autocomplete
		var table = '<?php if(isset($_GET['table'])) echo $_GET['table']; ?>';
		$('#searchField').autocomplete(
		{
			source: './template/bootstrap/pages/autocomplete.php?table='+table,
			minLength: 1,
			select: function (event, ui)
			{
				$("#searchField").val(ui.item.label);
				$("#searchForm").submit();
			}
		});
	
		// bxSlider
		function getGridSize()
		{
			return (window.innerWidth < 600) ? 3 :
				   (window.innerWidth < 900) ? 3 : 4;
		}
		$('.slider_detail').bxSlider(
		{
			slideWidth: 183,
			minSlides: getGridSize(),
			maxSlides: getGridSize(),
			slideMargin: 5,
			moveSlides: 2,
			captions: true,
			infiniteLoop: false,
			hideControlOnEnd: true
		});
		
		// Collapse
		$('#collapse').on("hide.bs.collapse", function()
		{
			$('a.a-box-tool').html('<i class="fa fa-plus"></i>');
		});
		$("#collapse").on("show.bs.collapse", function()
		{
			$('a.a-box-tool').html('<i class="fa fa-minus"></i>');
		});
		
		// Datepicker
		$("#datepicker").datepicker(
		{
			dateFormat: 'dd/mm/yy'
		});
		
		// LazyLoad
		$('img.lazy').lazyload(
		{
			effect : 'fadeIn'
		});
		
		// LightGallery
		$("#affiche").lightGallery(
		{
			download: false,
			counter: false
		});
		$("#bandeannonce").lightGallery(
		{
			counter: false
		});

		// Modal
		$('#modalMemberDell').on('show.bs.modal', function (event)
		{
			var button = $(event.relatedTarget)
			var recipient = button.data('whatever')
			var modal = $(this)
			modal.find('.modal-content input').val(recipient)
		});
		$('#modalCategoryDell').on('show.bs.modal', function (event)
		{
			var button = $(event.relatedTarget)
			var recipient = button.data('whatever')
			var modal = $(this)
			modal.find('.modal-content input').val(recipient)
		});
		$('#modalMenuDell').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
			var recipient = button.data('whatever')
			var modal = $(this)
			modal.find('.modal-content input').val(recipient)
		});
		$('#modalMenuFilterDell').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
			var recipient = button.data('whatever')
			var modal = $(this)
			modal.find('.modal-content input').val(recipient)
		});

		//Select2
		$(".select2").select2({
			minimumResultsForSearch: Infinity,
			theme: "bootstrap",
			language: "fr"
		});
		$(".select2-pays").select2({
			placeholder: "Choisir votre pays",
			theme: "bootstrap",
			language: "fr"
		});
		<?php
			if(isset($table))
			{
				$select2_filter_query = $db->prepare('SELECT `id`, `name`, `type`, `sort`, `menu`, `position` FROM `site_menu_filter` WHERE `menu` = :menu ORDER BY `position`');
				$select2_filter_query->bindValue(':menu', $table, PDO::PARAM_INT);
				$select2_filter_query->execute();
				while ($select2_filter = $select2_filter_query->fetch())
				{
					echo '$(".select2-list-'.$select2_filter['type'].'").select2({';
						if($select2_filter['type'] == 'annee' || $select2_filter['type'] == 'note' || $select2_filter['type'] == 'reference' || $select2_filter['type'] == 'edition' || $select2_filter['type'] == 'zone') $tous = 'Toutes'; else $tous = 'Tous';
						echo 'placeholder: "'.$tous.' les '.$select2_filter['name'].'",';
						echo 'minimumResultsForSearch: Infinity,';
						echo 'theme: "bootstrap",';
						echo 'language: "fr"';
					echo '});';
				}
			}
			$select2_filter_query->closeCursor();
		?>
	});
</script>
