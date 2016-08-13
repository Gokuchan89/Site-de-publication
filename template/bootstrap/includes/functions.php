<?php
	/*
		=================================
		DECONNEXION
		=================================
	*/
	function logout()
	{
		unset($_SESSION['username']);
		ob_end_clean();
		header('Location: ./');
		exit();
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
		PROFIL -> SEXE
		=================================
	*/
	function sex($sex)
	{
		$sex = str_replace(array('1', '2'), array('Féminin', 'Masculin'), $sex);
		return $sex;
	}

	/*
		=================================
		PROFIL -> DATE DE NAISSANCE
		=================================
	*/
	function date_birthday($date)
	{
		$tabDate = explode('/' , $date);
		$date  = $tabDate[2].'-'.$tabDate[1].'-'.$tabDate[0];
		return $date;
	}

	/*
		=================================
		PROFIL -> AVATAR
		=================================
	*/
	function move_avatar($avatar, $username)
	{
		$extension = strtolower(substr(strrchr($avatar['name'], '.'), 1));
		$name = $username;
		$name = 'img/avatar/'.str_replace(' ','', $name).'.'.$extension;
		move_uploaded_file($avatar['tmp_name'], $name);
	}

	/*
		=================================
		LISTE -> PAGINATE
		=================================
	*/
	function paginate($url, $link, $total, $current, $adj=2)
	{
		// Initialisation des variables
		$prev = $current - 1; 			// numéro de la page précédente
		$next = $current + 1; 			// numéro de la page suivante
		$penultimate = $total - 1; 		// numéro de l'avant-dernière page
		$pagination = ''; 				// variable retour de la fonction : vide tant qu'il n'y a pas au moins 2 pages

		if ($total > 1)
		{
			// Remplissage de la chaîne de caractères à retourner
			$pagination .= "\n";

			/* =================================
			 *  Affichage du bouton [précédent]
			 * ================================= */
			if ($current == 2)
			{
				// la page courante est la 2, le bouton renvoie donc sur la page 1, remarquez qu'il est inutile de mettre $url{$link}1
				$pagination .= '<li><a href="'.$url.'"><i class="fa fa-angle-double-left"></i></a></li>';
			}
			elseif ($current > 2)
			{
				// la page courante est supérieure à 2, le bouton renvoie sur la page dont le numéro est immédiatement inférieur
				$pagination .= '<li><a href="'.$url.$link.$prev.'"><i class="fa fa-angle-double-left"></i></a></li>';
			} else {
				// dans tous les autres cas, la page est 1 : désactivation du bouton [précédent]
				$pagination .= '<li class="disabled"><a href="#"><i class="fa fa-angle-double-left"></i></a></li>';
			}

			/*
				* Début affichage des pages, l'exemple reprend le cas de 3 numéros de pages adjacents (par défaut) de chaque côté du numéro courant
				* - CAS 1 : il y a au plus 12 pages, insuffisant pour faire une troncature
				* - CAS 2 : il y a au moins 13 pages, on effectue la troncature pour afficher 11 numéros de pages au total
			*/

			/* ===============================================
			 *  CAS 1 : au plus 12 pages -> pas de troncature
			 * =============================================== */
			if ($total < 7 + ($adj * 2))
			{
				// Ajout de la page 1 : on la traite en dehors de la boucle pour n'avoir que index.php au lieu de index.php?p=1 et ainsi éviter le duplicate content
				$pagination .= ($current == 1) ? '<li class="active"><a href="#">1</a></li>' : '<li><a href="'.$url.'">1</a></li>'; // Opérateur ternaire : (condition) ? 'valeur si vrai' : 'valeur si fausse'

				// Pour les pages restantes on utilise itère
				for ($i=2; $i<=$total; $i++)
				{
					if ($i == $current)
					{
						// Le numéro de la page courante est mis en évidence
						$pagination .= '<li class="active"><a href="#">'.$i.'</a></li>';
					} else {
						// Les autres sont affichées normalement
						$pagination .= '<li><a href="'.$url.$link.$i.'">'.$i.'</a></li>';
					}
				}
			}

			/* =========================================
			 *  CAS 2 : au moins 13 pages -> troncature
			 * ========================================= */
			else {
				/*
					* Troncature 1 : on se situe dans la partie proche des premières pages, on tronque donc la fin de la pagination.
					* l'affichage sera de neuf numéros de pages à gauche ... deux à droite
					* 1 2 3 4 5 6 7 8 9 … 16 17
				*/
				if ($current < 2 + ($adj * 2))
				{
					// Affichage du numéro de page 1
					$pagination .= ($current == 1) ? '<li class="active"><a href="#">1</a></li>' : '<li><a href="' . $url . '">1</a></li>';

					// puis des huit autres suivants
					for ($i = 2; $i < 4 + ($adj * 2); $i++)
					{
						if ($i == $current)
						{
							$pagination .= '<li class="active"><a href="#">'.$i.'</a></li>';
						} else {
							$pagination .= '<li><a href="'.$url.$link.$i.'">'.$i.'</a>';
						}
					}

					// ... pour marquer la troncature
					$pagination .= '<li><a href="#">&hellip;</a></li>';

					// et enfin les deux derniers numéros
					$pagination .= '<li><a href="'.$url.$link.$penultimate.'">'.$penultimate.'</a></li>';
					$pagination .= '<li><a href="'.$url.$link.$total.'">'.$total.'</a></li>';
				}
				/*
					* Troncature 2 : on se situe dans la partie centrale de notre pagination, on tronque donc le début et la fin de la pagination.
					* l'affichage sera deux numéros de pages à gauche ... sept au centre ... deux à droite
					* 1 2 … 5 6 7 8 9 10 11 … 16 17
				*/
				elseif ((($adj * 2) + 1 < $current) && ($current < $total - ($adj * 2)))
				{
					// Affichage des numéros 1 et 2
					$pagination .= '<li><a href="'.$url.'">1</a></li>';
					$pagination .= '<li><a href="'.$url.$link.'2">2</a></li>';
					$pagination .= '<li><a href="#">&hellip;</a></li>';

					// les pages du milieu : les trois précédant la page courante, la page courante, puis les trois lui succédant
					for ($i = $current - $adj; $i <= $current + $adj; $i++)
					{
						if ($i == $current)
						{
							$pagination .= '<li class="active"><a href="#">'.$i.'</a></li>';
						} else {
							$pagination .= '<li><a href="'.$url.$link.$i.'">'.$i.'</a></li>';
						}
					}

					$pagination .= '<li><a href="#">&hellip;</a></li>';

					// et les deux derniers numéros
					$pagination .= '<li><a href="'.$url.$link.$penultimate.'">'.$penultimate.'</a></li>';
					$pagination .= '<li><a href="'.$url.$link.$total.'">'.$total.'</a></li>';
				}
				/*
					* Troncature 3 : on se situe dans la partie de droite, on tronque donc le début de la pagination.
					* l'affichage sera deux numéros de pages à gauche ... neuf à droite
					* 1 2 … 9 10 11 12 13 14 15 16 17
				*/
				else {
					// Affichage des numéros 1 et 2
					$pagination .= '<li><a href="'.$url.'">1</a></li>';
					$pagination .= '<li><a href="'.$url.$link.'2">2</a></li>';
					$pagination .= '<li><a href="#">&hellip;</a></li>';

					// puis des neuf derniers numéros
					for ($i = $total - (2 + ($adj * 2)); $i <= $total; $i++)
					{
						if ($i == $current)
						{
							$pagination .= '<li class="active"><a href="#">'.$i.'</a></li>';
						} else {
							$pagination .= '<li><a href="'.$url.$link.$i.'">'.$i.'</a></li>';
						}
					}
				}
			}

			/* ===============================
			 *  Affichage du bouton [suivant]
			 * =============================== */
			if ($current == $total)
			{
				$pagination .= '<li class="disabled"><a href="#"><i class="fa fa-angle-double-right"></i></a></li>';
			} else {
				$pagination .= '<li><a href="'.$url.$link.$next.'"><i class="fa fa-angle-double-right"></i></a></li>';
			}

			// Fermeture de la <div> d'affichage
			$pagination .= "\n";
		}
		return $pagination;
	}

	/*
		=================================
		DETAIL -> CLEAN IMG
		=================================
	*/
	function clean_img($texte)
	{
		$texte = mb_strtolower($texte, 'UTF-8');
		$texte = str_replace(" ", "_", $texte);
		$texte = str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å', 'î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 'ð', 'ù', 'û', 'ü', 'ú', 'ū', 'é', 'è', 'ê', 'ë', 'ç', 'ÿ', 'ñ'), array('a', 'a', 'a', 'a', 'a', 'a', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'e', 'e', 'e', 'e', 'c', 'y', 'n'), $texte);
		return $texte;
	}

	/*
		=================================
		DETAIL -> FILTERED + SEARCH
		=================================
	*/
	function filter($label, $value, $table_id, $table_name)
	{
		echo '<form method="POST" action="?op=list&table='.$table_id.'" style="display:inline;">';
			$liste = str_replace(' / ', ' - ', $value);
			$liste_search = explode(' - ', $liste);
			for ($i=0;$i<count($liste_search);$i++)
			{
				if (($i+1) == count($liste_search))
				{
					if (file_exists('./img/supports/'.$liste_search[$i].'.png'))
					{
						echo '<button type="submit" class="nobtn" name="'.$table_name.'_search_value_'.$label.'" value="'.$liste_search[$i].'"><img src="./img/supports/'.$liste_search[$i].'.png" style="max-width:82px;max-height:25px;" /></button>';
					} else {
						echo '<button type="submit" class="nobtn" name="'.$table_name.'_search_value_'.$label.'" value="'.$liste_search[$i].'"><div class="text-primary">'.$liste_search[$i].'</div></button>';
					}
				}
				else
				{
					if (file_exists('./img/supports/'.$liste_search[$i].'.png'))
					{
						echo '<button type="submit" class="nobtn" name="'.$table_name.'_search_value_'.$label.'" value="'.$liste_search[$i].'"><img src="./img/supports/'.$liste_search[$i].'.png" style="max-width:82px;max-height:25px;" /></button> / ';
					} else {
						echo '<button type="submit" class="nobtn" name="'.$table_name.'_search_value_'.$label.'" value="'.$liste_search[$i].'"><div class="text-primary">'.$liste_search[$i].'</div></button> / ';
					}
				}
			}
		echo '</form>';
	}

	function search($label, $value, $table_id, $table_name)
	{
		$liste = str_replace("\r", '|', $value);
		$liste_search = explode('|', $liste);
		for ($i=0;$i<count($liste_search);$i++)
		{
			$nom_search = explode(' : ', $liste_search[$i]);
			if (count($nom_search) > 1)
			{
				$nom_membre_search = $nom_search[1];
			} else {
				$nom_membre_search = '';
			}
			echo '<li>';
				echo '<form method="POST" action="?op=list&table='.$table_id.'">';
					echo '<button type="submit" class="nobtn-actor" name="'.$table_name.'_search_value" value="'.$nom_search[0].'">';
						$filename = './img/real_acteur/'.clean_img($nom_search[0]).'.jpg';
						if (file_exists($filename))
						{
							echo '<img src="'.$filename.'" title="'.$nom_search[0].'<br/>'.$nom_membre_search.'" />';
						} else {
							echo '<img src="./img/nobody.jpg" title="'.$nom_search[0].'<br/>'.$nom_membre_search.'" />';
						}
					echo '</button>';
				echo '</form>';
			echo '</li>';
		}
	}

	/*
		=================================
		DETAIL -> DATE DE SORTIE
		=================================
	*/
	function date_sortie($mois)
	{
		$mois = str_replace(array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'), array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'), $mois);
		return $mois;
	}
?>