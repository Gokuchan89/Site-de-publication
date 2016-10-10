<?php
	if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1)
	{
		header("location: ./");
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<?php include('./template/bootstrap/includes/header.php'); ?>
	<body>
		<?php include('./template/bootstrap/includes/navbar.php'); ?>
		<div class="container">
			<ol class="breadcrumb">
				<li><i class="fa fa-home"></i></li>
				<li>Administration</li>
				<li>Historique d'activité</li>
			</ol>
			<div class="panel panel-default">
				<div class="panel-heading">Historique d'activité</div>
				<div class="panel-body table-responsive">
					<table class="table table-bordered table-striped" id="log_list">
						<thead>
							<tr>
								<th>Date/Heure</th>
								<th>Identifiant</th>
								<th>Module</th>
								<th>Action</th>
								<th>Commentaire</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$logs = new Log_activite();
								$liste = $logs->getLog_activiteList();
							?>
							<?php foreach($liste as $log) { ?>
								<tr>
									<td><?php echo date('d/m/Y H:i:s', $log['date_time']); ?></td>
									<td><?php echo $log['username']; ?></td>
									<td><?php echo $log['module']; ?></td>
									<td><?php echo $log['action']; ?></td>
									<td><?php echo $log['comment']; ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php include('./template/bootstrap/includes/footer.php'); ?>
		<?php include('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>