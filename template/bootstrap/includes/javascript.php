<script src="./template/bootstrap/js/jquery.min.js"></script>
<script src="./template/bootstrap/js/jqueryui.min.js"></script>
<script src="./template/bootstrap/js/bootstrap.min.js"></script>
<script src="./template/bootstrap/plugins/lazyload/js/lazyload.min.js"></script>
<script src="./template/bootstrap/plugins/holder/js/holder.min.js"></script>
<script src="./template/bootstrap/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js"></script>
<script src="./template/bootstrap/plugins/select2/js/select2.full.min.js"></script>
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
		allowClear: true,
		theme: "bootstrap"
	});
	$(".select2-edition").select2({
		placeholder: "Toutes les éditions",
		minimumResultsForSearch: Infinity,
		allowClear: true,
		theme: "bootstrap"
	});
	$(".select2-filmvu").select2({
		placeholder: "Tous les vu/non vu",
		minimumResultsForSearch: Infinity,
		allowClear: true,
		theme: "bootstrap"
	});
	$(".select2-genre").select2({
		placeholder: "Tous les genres",
		minimumResultsForSearch: Infinity,
		allowClear: true,
		theme: "bootstrap"
	});
	$(".select2-annee").select2({
		placeholder: "Toutes les années",
		minimumResultsForSearch: Infinity,
		allowClear: true,
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
	var table = "<?php if(isset($_GET['table'])) echo $_GET['table']; else echo '' ?>";
	$('#autocomplete').autocomplete(
	{
		source: './template/bootstrap/pages/autocomplete.php?table='+table,
		minLength: 1
	})
</script>