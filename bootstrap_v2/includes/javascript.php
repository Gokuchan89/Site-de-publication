<script src="./template/bootstrap_v2/js/jquery.min.js"></script>
<script src="./template/bootstrap_v2/js/jqueryui.min.js"></script>
<script src="./template/bootstrap_v2/js/bootstrap.min.js"></script>
<script src="./template/bootstrap_v2/plugins/lazyload/js/lazyload.min.js"></script>
<script src="./template/bootstrap_v2/plugins/holder/js/holder.min.js"></script>
<script src="./template/bootstrap_v2/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js"></script>
<script src="./template/bootstrap_v2/plugins/select2/js/select2.full.min.js"></script>
<script src="./template/bootstrap_v2/plugins/lightgallery/js/lightgallery.js"></script>
<script src="./template/bootstrap_v2/plugins/lightgallery/js/lg-video.js"></script>
<script src="./template/bootstrap_v2/plugins/bxslider/js/jquery.bxslider.min.js"></script>
<script>
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
	$('#autocomplete').autocomplete(
	{
		source: './template/bootstrap/pages/autocomplete.php?table='+table,
		minLength: 1
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
		slideWidth: 175,
		minSlides: 3,
		maxSlides: 6,
		slideMargin: 10,
		captions: true,
		pager: false,
		auto: true
	});

	$('.slider_detail').bxSlider(
	{
		slideWidth: 180,
		minSlides: 3,
		maxSlides: 4,
		slideMargin: 10,
		captions: true,
		pager: false,
		infiniteLoop: false,
		hideControlOnEnd: true
	});
</script>