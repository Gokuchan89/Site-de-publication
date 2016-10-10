<!DOCTYPE html>
<html>
	<?php include('./template/bootstrap/includes/header.php'); ?>
	<body>
		<?php include('./template/bootstrap/includes/navbar.php'); ?>
		<div class="container">
			<?php
				$setting_message_home = new Setting();
				$setting_message_home->getSettingDBKey('message_home');
				if (!empty($setting_message_home->getValue()))
				{
					echo "<div class=\"panel panel-default\">";
						echo "<div class=\"panel-heading\">Message de la part de l'administrateur</div>";
						echo "<div class=\"panel-body\">";
							echo $setting_message_home->getValue();
						echo "</div>";
					echo "</div>";
				}
			?>
		</div>
		<?php include('./template/bootstrap/includes/footer.php'); ?>
		<?php include('./template/bootstrap/includes/javascript.php'); ?>
	</body>
</html>