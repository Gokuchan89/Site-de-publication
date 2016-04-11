<?php
    if(isset($_GET['query']))
	{
        $t = htmlentities($_GET['table']);
        $q = htmlentities($_GET['query']);
 
		require_once('../../../includes/mysqlConstants.php');
		require_once('../../../includes/mysqlConnector.php');
		
		$query = $db->prepare('SELECT `id`, `name`, `table` FROM site_menu WHERE `id` = :id');
		$query->bindValue(':id', $t, PDO::PARAM_STR);
		$query->execute();
		$menu = $query->fetch();
		$query->CloseCursor();
	 
        // Requête SQL
        $requete = 'SELECT * FROM '.$menu['table'].' WHERE TitreVF LIKE "'.$q.'%" LIMIT 0, 10';
 
        // Exécution de la requête SQL
        $resultat = $db->query($requete);
 
        // On parcourt les résultats de la requête SQL
        while($donnees = $resultat->fetch(PDO::FETCH_ASSOC))
		{
            // On ajoute les données dans un tableau
            $suggestions['suggestions'][] = $donnees['TitreVF'];
        }
 
        // On renvoie le données au format JSON pour le plugin
        echo json_encode($suggestions);
    }
?>