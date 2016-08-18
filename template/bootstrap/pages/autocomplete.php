<?php
    if (isset($_GET['term']))
	{
		$suggestions = array();
		
        $table = htmlentities($_GET['table']);
        $term = htmlentities($_GET['term']);
 
		require_once('../../../includes/mysqlConstants.php');
		require_once('../../../includes/mysqlConnector.php');
		
		$query = $db->prepare('SELECT `id`, `table` FROM `site_menu` WHERE `id` = :id');
		$query->bindValue(':id', $table, PDO::PARAM_STR);
		$query->execute();
		$menu = $query->fetch();
		$query->CloseCursor();
 
        // Exécution de la requête SQL
        $requete = $db->query('SELECT DISTINCT `TitreVF` FROM `'.$menu['table'].'` WHERE (`TitreVF` LIKE "%'.$term.'%" OR `TitreVO` LIKE "%'.$term.'%" OR `Acteurs` LIKE "%'.$term.'%" OR `Realisateurs` LIKE "%'.$term.'%") ORDER BY `TitreVF` LIMIT 0, 10');
 
        // On parcourt les résultats de la requête SQL
        while ($donnee = $requete->fetch())
		{
            // On ajoute les données dans un tableau
            $suggestions[] = $donnee['TitreVF'];
        }
 
        // On renvoie le données au format JSON pour le plugin
        echo json_encode($suggestions);
    }
?>
