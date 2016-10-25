<?php
	ini_set("session.gc_maxlifetime", 28800);
	session_name("intranet");
	session_start();

	require_once("../class/_classLoader.php");
	
	// On d�finit la configuration :
	$nbr_chiffres = 6; // Nombre de chiffres qui formeront le nombre

	// L�, on d�finit le header de la page pour la transformer en image
	header ("Content-type: image/png");
	
	// L�, on cr�e notre image
	$_img = imagecreatefrompng("../img/bg_captcha.png");

	// On d�finit maintenant les couleurs
	// Couleur de fond :
	$arriere_plan = imagecolorallocate($_img, 0, 0, 0); // Au cas o� on n'utiliserait pas d'image de fond, on utilise cette couleur-l�.
	// Autres couleurs :
	$avant_plan = imagecolorallocate($_img, 255, 255, 255); // Couleur des chiffres

	// Ici on cr�e la variable qui contiendra le nombre al�atoire
	$i = 0;
	while($i < $nbr_chiffres)
	{
		$chiffre = mt_rand(0, 9); // On g�n�re le nombre al�atoire
		$chiffres[$i] = $chiffre;
		$i++;
	}
	$nombre = null;
	
	// On explore le tableau $chiffres afin d'y afficher toutes les entr�es qui s'y trouvent
	foreach ($chiffres as $caractere)
	{
		$nombre .= $caractere;
	}
	
	// On a fini de cr�er le nombre al�atoire, on le rentre maintenant dans une variable de session
	$_SESSION['aleat_nbr'] = $nombre;
	
	// On d�truit les variables inutiles :
	unset($chiffre);
	unset($i);
	unset($caractere);
	unset($chiffres);
	
	imagestring($_img, 5, 18, 8, $nombre, $avant_plan);

	imagepng($_img);
?>