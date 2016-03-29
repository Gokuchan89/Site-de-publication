<script src="./template/bootstrap/js/jquery.min.js"></script>
<script src="./template/bootstrap/js/bootstrap.min.js"></script>
<script src="./template/bootstrap/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js" type="text/javascript"></script>
<script>
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
</script>