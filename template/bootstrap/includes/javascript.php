<!-- JQUERY 3.1.0 -->
<script src="./template/adminlte/js/jquery.min.js"></script>
<!-- JQUERYUI 1.12.0 -->
<script src="./template/adminlte/js/jqueryui.min.js"></script>
<!-- BOOTSTRAP 3.3.7 -->
<script src="./template/adminlte/js/bootstrap.min.js"></script>
<!-- ADMINLTE 2.3.5 -->
<script src="./template/adminlte/js/adminlte.min.js"></script>
<!-- HOLDER 2.9.3 -->
<script src="./template/adminlte/plugins/holder/js/holder.min.js"></script>
<!-- LAZYLOAD 1.9.7 -->
<script src="./template/adminlte/plugins/lazyload/js/lazyload.min.js"></script>
<!-- LIGHTGALLERY 1.2.22 -->
<script src="./template/adminlte/plugins/lightgallery/js/lightgallery.min.js"></script>
<!-- LIGHTGALLERY LG-VIDEO 1.2.22 -->
<script src="./template/adminlte/plugins/lightgallery/js/lg-video.min.js"></script>
<!-- SELECT2 4.0.3 -->
<script src="./template/adminlte/plugins/select2/js/select2.full.min.js"></script>
<!-- SELECT2 LANG -->
<script src="./template/adminlte/plugins/select2/js/i18n/fr.js"></script>
<script>
	$(document).ready(function()
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
		})
		
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

		//Select2
		$(".select2").select2({
			minimumResultsForSearch: Infinity,
			language: "fr"
		});
		$(".select2-support").select2({
			placeholder: "Tous les supports",
			minimumResultsForSearch: Infinity,
			language: "fr"
		});
		$(".select2-commentaires").select2({
			placeholder: "Tous les types",
			minimumResultsForSearch: Infinity,
			language: "fr"
		});
		$(".select2-edition").select2({
			placeholder: "",
			minimumResultsForSearch: Infinity,
			language: "fr"
		});
		$(".select2-filmvu").select2({
			placeholder: "Tous les vu/non vu",
			minimumResultsForSearch: Infinity,
			language: "fr"
		});
		$(".select2-pays").select2({
			placeholder: "",
			minimumResultsForSearch: Infinity,
			language: "fr"
		});
		$(".select2-genre").select2({
			placeholder: "Tous les genres",
			minimumResultsForSearch: Infinity,
			language: "fr"
		});
		$(".select2-annee").select2({
			placeholder: "Toutes les années",
			minimumResultsForSearch: Infinity,
			language: "fr"
		});
	});
</script>