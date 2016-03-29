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

	/*
		=================================
		PROFILE -> SEXE
		=================================
	*/
	function sex($sex)
	{
		$sex = str_replace(array('1', '2'), array('Féminin', 'Masculin'), $sex);
		return $sex;
	}

	/*
		=================================
		PROFILE -> AVATAR
		=================================
	*/
	function move_avatar($avatar, $username)
	{
		$extension = strtolower(substr(strrchr($avatar['name'], '.'), 1));
		$name = $username;
		$name = 'img/avatar/'.str_replace(' ','', $name).'.'.$extension;
		move_uploaded_file($avatar['tmp_name'], $name);
	}
?>