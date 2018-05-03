<?php

try {
	$db_options = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", // On force l'encodage en utf8
	    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // On récupère tous les résultats en tableau associatif
	    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, // On affiche des warnings pour les erreurs,                        |    à commenter
	    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // On affiche des exceptions pour les erreurs                      |     en prod ???
	);

	// Au départ, j'avais écrit en dur le nom des paramètres de connexion :
	//    ... = new PDO('mysql:host=hhhhhhhhhhhhhhh;dbname=bbbbbbbbbbbbb, uuuuuuuuuuuuuuuu, pppppppppppppppp, $db_options')

	$dbConnex = new PDO('mysql:host='.ADR_BDD_HBG.';dbname='.NOM_BDD_HBG, USERNAME_BDD_HBG, USERPSWD_BDD_HBG, $db_options);
}
catch(Exception $e) {
	//attrappe les éventuelles erreurs de connexion
	echo 'Erreur de connexion : ' . $e->getMessage();
}