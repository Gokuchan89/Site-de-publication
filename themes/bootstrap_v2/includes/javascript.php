<!-- JQUERY 2.2.3 -->
<script src="./template/bootstrap_v2/js/jquery.min.js"></script>
<!-- JQUERYUI 1.11.4 -->
<script src="./template/bootstrap_v2/js/jqueryui.min.js"></script>
<!-- BOOTSTRAP 3.3.6 -->
<script src="./template/bootstrap_v2/js/bootstrap.min.js"></script>
<!-- LAZYLOAD 1.9.5 -->
<script src="./template/bootstrap_v2/plugins/lazyload/js/lazyload.min.js"></script>
<!-- HOLDER 2.9.0 -->
<script src="./template/bootstrap_v2/plugins/holder/js/holder.min.js"></script>
<!-- JASNY BOOTSTRAP 3.1.3 -->
<script src="./template/bootstrap_v2/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js"></script>
<!-- SELECT2 4.0.2 -->
<script src="./template/bootstrap_v2/plugins/select2/js/select2.full.min.js"></script>
<!-- LIGHTGALLERY 1.2.18 -->
<script src="./template/bootstrap_v2/plugins/lightgallery/js/lightgallery.js"></script>
<!-- LIGHTGALLERY LG-VIDEO 1.2.18 -->
<script src="./template/bootstrap_v2/plugins/lightgallery/js/lg-video.js"></script>
<!-- BXSLIDER 4.2.5 -->
<script src="./template/bootstrap_v2/plugins/bxslider/js/bxslider.min.js"></script>
<script>
	$(document).ready( function()
	{
		// LazyLoad
		$('img.lazy').lazyload(
		{
			effect : 'fadeIn'
		});

		// Modal
		$('#modalMembersDell').on('show.bs.modal', function (event)
		{
			var button = $(event.relatedTarget)
			var recipient = button.data('whatever')
			var modal = $(this)
			modal.find('.modal-content input').val(recipient)
		})
		$('#modalCategoryDell').on('show.bs.modal', function (event)
		{
			var button = $(event.relatedTarget)
			var recipient = button.data('whatever')
			var modal = $(this)
			modal.find('.modal-body input').val(recipient)
		})
		$('#modalMenuDell').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
			var recipient = button.data('whatever')
			var modal = $(this)
			modal.find('.modal-body input').val(recipient)
		})

		//Select2
		$(".select2").select2({
			minimumResultsForSearch: Infinity,
			theme: "bootstrap"
		});
		$(".select2-pays").select2({
			placeholder: "Choisir votre pays",
			theme: "bootstrap"
		});
		$(".select2-support").select2({
			placeholder: "Tous les supports",
			minimumResultsForSearch: Infinity,
			theme: "bootstrap"
		});
		$(".select2-edition").select2({
			placeholder: "Toutes les éditions",
			minimumResultsForSearch: Infinity,
			theme: "bootstrap"
		});
		$(".select2-filmvu").select2({
			placeholder: "Tous les vu/non vu",
			minimumResultsForSearch: Infinity,
			theme: "bootstrap"
		});
		$(".select2-genre").select2({
			placeholder: "Tous les genres",
			minimumResultsForSearch: Infinity,
			theme: "bootstrap"
		});
		$(".select2-annee").select2({
			placeholder: "Toutes les années",
			minimumResultsForSearch: Infinity,
			theme: "bootstrap"
		});
		
		// Datepicker
		$("#datepicker").datepicker(
		{
			dateFormat: 'dd/mm/yy'
		});
		
		// Drop-menu
		$('.drop-menu li a').click(function()
		{
			var selText = $(this).text();
			var selName = $(this).attr('name');
			$(this).parents('.collapse').find('.champ_recherche').val(selName);
			$(this).parents('.collapse').find('.drop-toggle').html(selText);
		});
		
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
		})
		
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
	
		// bxSlider
		$('.slider_home').bxSlider(
		{
			slideWidth: 195,
			minSlides: 6,
			maxSlides: 6,
			slideMargin: 5,
			moveSlides: 2,
			captions: true,
			auto: true
		});
		$('.slider_detail').bxSlider(
		{
			slideWidth: 195,
			minSlides: 4,
			maxSlides: 4,
			slideMargin: 5,
			moveSlides: 2,
			captions: true,
			infiniteLoop: false,
			hideControlOnEnd: true
		});
	});
</script>