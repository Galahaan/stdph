<?php

	//////////////////////////////////////////////////////////////////////////////////////////
	///////////////                                                            ///////////////
	///////////////                Coordonnées de la pharmacie                 ///////////////
	///////////////                                                            ///////////////
	//////////////////////////////////////////////////////////////////////////////////////////

	// Nom de la pharmacie
	define("NOM_PHARMA", "Pharmacie des Tilleuls");
	define("STI_PHARMA", "       à Gaël");

	// Adresse de la pharmacie
	define("ADR_PHARMA_L1", "place des Tilleuls");
	define("ADR_PHARMA_L2", "");
	define("CP_PHARMA",     "35290");
	define("VIL_PHARMA",    "Gaël");

	// Tel, fax, mail, facebook, google+
	define("TEL_PHARMA_DECO", "01 23 45 67 89");
	define("TEL_PHARMA_UTIL", "+33123456789");
	define("FAX_PHARMA_DECO", "00 12 34 56 78");
	define("ADR_MAIL_PHARMA", "contact@pharmaciedestilleuls.fr");
	define("ADR_FB_PHARMA",   "https://www.facebook.com/Pharmacie-Le-Reste-700447003388902");
	define("ADR_GG_PHARMA",   "https://plus.google.com/113407799173132476603/about");

	// Mots-clés pour le référencement
	define("MC_NOM_PHARMA", "des tilleuls");
	define("MC_QUARTIER",   "gaël");
	define("MC_CP",         "35290");
	define("MC_1",          "tilleuls");
	define("MC_2",          "gaëlite");

	// 'home' du site sur le serveur de l'hébergeur (functions.php)
	define("HOME", "/home/bigouigfiy/"); // utilisé uniquement dans require_onceCLR qui n'est utilisée nulle part !

	//////////////////////////////////////////////////////////////////////////////////////////
	///////////////                                                            ///////////////
	///////////////                  Base de données hébergée                  ///////////////
	///////////////                                                            ///////////////
	//////////////////////////////////////////////////////////////////////////////////////////

	// adresse du serveur de la BDD (initDB.php)
	define("ADR_BDD_HBG", "bigouigfiytofbdd.mysql.db");

	// nom de la BDD (initDB.php)
	define("NOM_BDD_HBG", "bigouigfiytofbdd");

	// utilisateur de la BDD (initDB.php)
	define("USERNAME_BDD_HBG", "bigouigfiytofbdd");
	define("USERPSWD_BDD_HBG", "Mdp2bigouig");

	// nom de la table 'clients' pour cette BDD (inscription.php et connexion.php)
	define("TABLE_CLIENTS", "clientsBIG");

	//////////////////////////////////////////////////////////////////////////////////////////
	///////////////                                                            ///////////////
	///////////////        Infos hébergeur concernant l'envoi des mails        ///////////////
	///////////////                                                            ///////////////
	///////////////      (pages prepaOrdonnance, prepaCommande et contact)     ///////////////
	///////////////                                                            ///////////////
	//////////////////////////////////////////////////////////////////////////////////////////

	// adresse de l'expéditeur des mails via l'hébergeur du site :
	define("ADR_EXP_HBG", "bigouigfiy@cluster020.hosting.ovh.net");

	// nom explicite pour l'expéditeur des mails : (possible avec accents !)
	define("LABEL_EXP", "Site pharmacie");

	// adresse du site de la pharmacie :
	define("ADRESSE_SITE_PHARMACIE", "http://bigouig.fr/");
	define("S_ADRESSE_SITE_PHARMACIE", "https://bigouig.fr/");
	define("W_ADRESSE_SITE_PHARMACIE", "http://www.bigouig.fr/");
	define("SW_ADRESSE_SITE_PHARMACIE", "https://www.bigouig.fr/");

	// adresse mail de la pharmacie :
	define("MAIL_DEST_PHARMA", "clr.tstph@use.startmail.com");

	// pour vérifier les 1ers mails officiels :
//	define("MAIL_DEST_CLR",    "clr.tstph@use.startmail.com");

	// taille max de la pièce jointe : 2 Mo = 2097152 octets
	define("TAILLE_MAX_PJ", 2097152);

	// extensions autorisées pour la pièce jointe : cf aussi ligne : " switch ($extension) " en ligne 177 de prepaCommande
	define("LISTE_EXT_AUTORISEES", '".jpe, .jpg, .jpeg, .png, .gif, .pdf"');

	//////////////////////////////////////////////////////////////////////////////////////////
	///////////////                                                            ///////////////
	///////////////       Nb caractères min et max dans les formulaires        ///////////////
	///////////////                                                            ///////////////
	///////////////      (pages prepaOrdonnance, prepaCommande et contact)     ///////////////
	///////////////                                                            ///////////////
	//////////////////////////////////////////////////////////////////////////////////////////

	// Nombre de caractères min et max pour les nom et prénom :
	define("NB_CAR_MIN", 2);
	define("NB_CAR_MAX", 40);

	// si on veut utiliser la vérification naturelle du HTML :
	define("NB_CAR_MIN_HTM", 1);
	define("NB_CAR_MAX_HTM", 40);

	// Nombre de caractères min et max pour le texte libre :
	define("NB_CAR_MIN_MESSAGE", 5);
	define("NB_CAR_MAX_MESSAGE", 1000);

	// si on veut utiliser la vérification naturelle du HTML :
	define("NB_CAR_MIN_MESSAGE_HTM", 1);
	define("NB_CAR_MAX_MESSAGE_HTM", 1000);

	//////////////////////////////////////////////////////////////////////////////////////////
	///////////////                                                            ///////////////
	///////////////           Horaires d'ouverture de la pharmacie             ///////////////
	///////////////                                                            ///////////////
	///////////////           (ctes utilisées dans functions.php)              ///////////////
	///////////////                                                            ///////////////
	//////////////////////////////////////////////////////////////////////////////////////////

	// 'D' pour le format décimal, 'H' pour le format horaire

	// le matin, de 8h30 ...
	define('OMATD', 8.5);
	define('OMATH', '8h30');

	// ... à 12h30
	define('FMATD', 12.5);
	define('FMATH', '12h30');

	// l'après-midi, de 14h ...
	define('OAMID', 14);
	define('OAMIH', '14h');

	// ... à 19h30							pour l'instant je n'utilise pas les constantes '...H'
	define('FAMID', 19.5);
	define('FAMIH', '19h30');

	// le samedi matin, de 9h ...
	define('SA_OMATD', 9);
	define('SA_OMATH', '9h');

	// ... à 16h
	define('SA_FAMID', 16);
	define('SA_FAMIH', '16h');

	// Compte à rebours avant ouverture / fermeture
	define('REBOURSD', 0.25);		// compte à rebours en 'heure décimale', ie que   0.25 = 15 mn
	define('PAS_DE_REBOURS', 5);	// en minutes ( ex. ... dans moins de 15, 10, 5 mn )

	// Particularités de la pharmacie : lignes de bus / tram / métro, parking privé, ...
	define('PARTICULARITES_PHARMACIE',
		"<p>La pharmacie dispose d'un parking pour sa clientèle.</p>
		<p>En <span>chronobus C6</span>, descendez à l'arrêt <span>St Joseph de Porterie</span> :
			 la pharmacie est alors à moins d'une minute.</p>");

	//////////////////////////////////////////////////////////////////////////////////////////
	///////////////                                                            ///////////////
	///////////////                     Horaires de garde                      ///////////////
	///////////////                                                            ///////////////
	///////////////                      (pharmaDeGarde)                       ///////////////
	///////////////                                                            ///////////////
	//////////////////////////////////////////////////////////////////////////////////////////


	// Heure au-delà de laquelle il faut se rendre au commissariat.
	// Si les gardes fonctionnent sans commissariat (ex. Vendée) -> mettre la constante _D à "X"
	define('HEURE_SOIR_POLICE_D', 20.5);
	define('HEURE_SOIR_POLICE_H', "20h30");
	// Nantes : 20h30
	// Brest  : 20h00

	// Heure jusqu'à laquelle il faut s'adresser au commissariat
	define('HEURE_MATIN_POLICE_D', 8);
	define('HEURE_MATIN_POLICE_H', "8h");
	// Nantes : 8h00
	// Brest  : 9h00

	define('ADRESSE_POLICE', "6 place Waldeck-Rousseau à Nantes");
?>