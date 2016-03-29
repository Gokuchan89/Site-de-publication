<?php
	/*
		=================================
		RAFRAICHISSEMENT
		=================================
	*/
	function refresh($url)
	{
		header('Location: '.$url);
	}

	/*
		=================================
		DECONNEXION
		=================================
	*/
	function logout()
	{
		unset($_SESSION['username']);
		refresh('./');
	}

	/*
		=================================
		RANG
		=================================
	*/
	function rank($rank)
	{
		$rank = str_replace(array('1', '2', '3'), array('Inviter', 'Membre', 'Administrateur'), $rank);
		return $rank;
	}
?>