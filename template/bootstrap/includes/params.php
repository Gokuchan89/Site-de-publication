<?php
	/*
		=================================
		REGLAGE
		=================================
	*/
	$order_array = array('TitreVF', 'TitreVF DESC', 'Annee', 'Annee DESC');
	if (!isset($_SESSION['option_order'])) $_SESSION['option_order'] = 'TitreVF';
	if (isset($_POST['option_order']) && in_array($_POST['option_order'], $order_array)) $_SESSION['option_order'] = $_POST['option_order'];
	$option_order = $_SESSION['option_order'];
	
	$nb_elements_array = array('6', '12', '18', '24', '30', '36');
	if (!isset($_SESSION['option_nb_elements'])) $_SESSION['option_nb_elements'] = '24';
	if (isset($_POST['option_nb_elements']) && in_array($_POST['option_nb_elements'], $nb_elements_array)) $_SESSION['option_nb_elements'] = $_POST['option_nb_elements'];
	$option_nb_elements = $_SESSION['option_nb_elements'];

	$dp_type_array = array('liste', 'galerie', 'table');
	if (!isset($_SESSION['option_dp_type'])) $_SESSION['option_dp_type'] = 'galerie';
	if (isset($_POST['option_dp_type']) && in_array($_POST['option_dp_type'], $dp_type_array)) $_SESSION['option_dp_type'] = $_POST['option_dp_type'];
	$option_dp_type = $_SESSION['option_dp_type'];
?>