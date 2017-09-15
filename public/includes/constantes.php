<?php
	///////////////////////////////////////////////////////////////////////////////////////////////
	/////
	/////     Pour les pages      prepaOrdonnance, prepaCommande et Contact
	/////
	///////////////////////////////////////////////////////////////////////////////////////////////

	// adresse de l'expéditeur des mails via l'hébergeur du site :
	//define("ADR_EXP_HEBERGEUR", "à compléter");																		//				à compléter         +++++++++
	define("ADR_EXP_HEBERGEUR", "bigouigfiy@cluster020.hosting.ovh.net");

	// nom explicite pour l'expéditeur des mails : (possible avec accents !)
	define("LABEL_EXP", "Site pharmacie");

	// adresse du site de la pharmacie :
	define("ADRESSE_SITE_PHARMACIE", "http://bigouig.fr/");
	define("S_ADRESSE_SITE_PHARMACIE", "https://bigouig.fr/");
	define("W_ADRESSE_SITE_PHARMACIE", "http://www.bigouig.fr/");
	define("SW_ADRESSE_SITE_PHARMACIE", "https://www.bigouig.fr/");
	//define("ADRESSE_SITE_PHARMACIE", "http://pharmacielereste.fr/");
	//define("S_ADRESSE_SITE_PHARMACIE", "https://pharmacielereste.fr/");
	//define("W_ADRESSE_SITE_PHARMACIE", "http://www.pharmacielereste.fr/");
	//define("SW_ADRESSE_SITE_PHARMACIE", "https://www.pharmacielereste.fr/");

	// adresse mail de la pharmacie :
	//define("MAIL_DEST_PHARMA", "phcie.lereste@perso.alliadis.net");
	define("MAIL_DEST_PHARMA", "bk24tsxnt@use.startmail.com");

	// taille max de la pièce jointe : 2 Mo = 2097152 octets
	define("TAILLE_MAX_PJ", 2097152);

	// extensions autorisées pour la pièce jointe : cf aussi ligne : " switch ($extension) " en ligne 177 de prepaCommande
	define("LISTE_EXT_AUTORISEES", '".jpe, .jpg, .jpeg, .png, .gif, .pdf"');

	// Nombre de caractères min et max pour les nom et prénom :
	define("NB_CAR_MIN", 2);
	define("NB_CAR_MAX", 40);

	// si on veut utiliser la vérification naturelle du HTML :
	define("NB_CAR_MIN_HTM", 2);
	define("NB_CAR_MAX_HTM", 40);

	// Nombre de caractères min et max pour le texte libre :
	define("NB_CAR_MIN_MESSAGE", 5);
	define("NB_CAR_MAX_MESSAGE", 1000);

	// si on veut utiliser la vérification naturelle du HTML :
	define("NB_CAR_MIN_MESSAGE_HTM", 1);
	define("NB_CAR_MAX_MESSAGE_HTM", 1000);

	///////////////////////////////////////////////////////////////////////////////////////////////
	/////
	/////     Pour la page      functions
	/////
	/////		(dont certaines fonctions sont dédiées aux horaires)
	/////
	///////////////////////////////////////////////////////////////////////////////////////////////

	// Horaires d'ouverture :

	// le matin, de 8h30 ...
	define('OUV_MAT', 8.5);

	// ... à 12h30
	define('FER_MID', 12.5);

	// l'après-midi, de 14h ...
	define('OUV_AMI', 14);    // horaires au format DECIMAL

	// ... à 19h30
	define('FER_SOI', 19.5);

	// le samedi matin, de 9h ...
	define('OUV_SAM', 9);

	// ... à 16h
	define('FER_SAM', 16);

	// Compte à rebours avant ouverture / fermeture
	define('REBOURS', 0.25);		// compte à rebours en 'heure décimale', ie que   0.25 = 15 mn
	define('PAS_DE_REBOURS', 5);	// en minutes ( ex. ... dans moins de 15, 10, 5 mn )
?>