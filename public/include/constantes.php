<?php

	//////////////////////////////////////////////////////////////////////////////////////////
	///////////////                                                            ///////////////
	///////////////                Coordonnées de la pharmacie                 ///////////////
	///////////////                                                            ///////////////
	//////////////////////////////////////////////////////////////////////////////////////////

	// Nom de la pharmacie
	define("NOM_PHARMA", "Pharmacie Le Reste");
	define("STI_PHARMA", "Nantes, quartier Saint-Joseph de Porterie");

	// Adresse de la pharmacie
	define("ADR_PHARMA_L1", "21 rue du Bêle");
	define("ADR_PHARMA_L2", "");
	define("CP_PHARMA",     "44300");
	define("VIL_PHARMA",    "Nantes");

	// Tel, fax, mail, facebook, google+
	define("TEL_PHARMA_DECO", "02 40 25 15 80");
	define("TEL_PHARMA_UTIL", "+33240251580");
	define("FAX_PHARMA_DECO", "02 40 30 06 56");
	define("ADR_MAIL_PHARMA", "contact@pharmacielereste.fr");
	define("ADR_FB_PHARMA",   "https://www.facebook.com/Pharmacie-Le-Reste-700447003388902");
	define("ADR_GG_PHARMA",   "https://plus.google.com/113407799173132476603/about");

	// Mots-clés pour le référencement
	define("MC_NOM_PHARMA", "le reste");
	define("MC_QUARTIER",   "saint-joseph-de-porterie");
	define("MC_CP",         "44300");
	define("MC_1",          "joseph");
	define("MC_2",          "porterie");

	// 'home' du site sur le serveur de l'hébergeur (functions.php)
	define("HOME", "/home/pharmacihc/"); // utilisé uniquement dans require_onceCLR qui n'est utilisée nulle part !

	//////////////////////////////////////////////////////////////////////////////////////////
	///////////////                                                            ///////////////
	///////////////                  Base de données hébergée                  ///////////////
	///////////////                                                            ///////////////
	//////////////////////////////////////////////////////////////////////////////////////////

	// adresse du serveur de la BDD (initDB.php)
	define("ADR_BDD_HBG", "pharmacihctofbdd.mysql.db");

	// nom de la BDD (initDB.php)
	define("NOM_BDD_HBG", "pharmacihctofbdd");

	// utilisateur de la BDD (initDB.php)
	define("USERNAME_BDD_HBG", "pharmacihctofbdd");
	define("USERPSWD_BDD_HBG", "Mdp2pharmacihc");

	// nom de la table 'clients' pour cette BDD (inscription.php et connexion.php)
	define("TABLE_CLIENTS", "clientsVLR01");

	//////////////////////////////////////////////////////////////////////////////////////////
	///////////////                                                            ///////////////
	///////////////        Infos hébergeur concernant l'envoi des mails        ///////////////
	///////////////                                                            ///////////////
	///////////////      (pages prepaOrdonnance, prepaCommande et contact)     ///////////////
	///////////////                                                            ///////////////
	//////////////////////////////////////////////////////////////////////////////////////////

	// adresse de l'expéditeur des mails via l'hébergeur du site :
	define("ADR_EXP_HBG", "pharmacihc@cluster021.hosting.ovh.net");

	// nom explicite pour l'expéditeur des mails : (possible avec accents !)
	define("LABEL_EXP", "Site pharmacie");

	// adresse du site de la pharmacie :
	define("ADRESSE_SITE_PHARMACIE", "http://pharmacielereste.fr/");
	define("S_ADRESSE_SITE_PHARMACIE", "https://pharmacielereste.fr/");
	define("W_ADRESSE_SITE_PHARMACIE", "http://www.pharmacielereste.fr/");
	define("SW_ADRESSE_SITE_PHARMACIE", "https://www.pharmacielereste.fr/");

	// adresse mail de la pharmacie :
	define("MAIL_DEST_PHARMA", "phcie.lereste@perso.alliadis.net");

	// pour vérifier les 1ers mails officiels :
	define("MAIL_DEST_CLR",    "clr.tstph@use.startmail.com");

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
?>