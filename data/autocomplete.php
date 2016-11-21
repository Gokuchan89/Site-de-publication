<?php
    if (isset($_GET['term']))
	{
		require_once("../class/_classLoader.php");
		
		$suggestions = array();
		
        $id = htmlentities($_GET['id']);
        $term = htmlentities($_GET['term']);
		
		$menu_list = new Menu();
		$menu_list->getMenuDBID($id);
					
		$search_list = new Table;
		$search_list = $search_list->getAutocompleteList($menu_list->getNametable(), $term);
		
		// On parcourt les résultats de la requête SQL
		foreach ($search_list as $search => $val_search)
		{
			// On ajoute les données dans un tableau
			$suggestions[] = $val_search['TitreVF']." (".$val_search['Annee'].")";
		}
		
		 // On renvoie le données au format JSON pour le plugin
        echo json_encode($suggestions);
    }
?>
