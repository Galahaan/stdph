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

// 'home' du site sur le serveur de l'hébergeur (fonctions.php)
define("HOME", "/home/bigouigfiy/"); // utilisé uniquement dans require_onceCLR qui n'est utilisée nulle part !

//////////////////////////////////////////////////////////////////////////////////////////
///////////////                                                            ///////////////
///////////////                       Mentions légales                     ///////////////
///////////////                                                            ///////////////
//////////////////////////////////////////////////////////////////////////////////////////


///////////////                      données spécifiques                   ///////////////

// Pharmacien titulaire
define("PHIEN_TITULAIRE", "Hubert des Rosiers");

// N° inscr. Ordre (RPPS)
define("PHIEN_RPPS", "12345678901");

// N° de licence de la pharmacie
define("PHIE_LICENCE", "35#123456");

// N° individuel d'identification relatif à l'assujettissement à la TVA
define("PHIE_TVA", "FR12345678901");

// N° d'inscription au Registre du Commerce et des Sociétés (SIREN)
define("PHIE_SIRET", "12345678900012");

///////////////                       données générales                    ///////////////

// Code APE
define("PHIE_APE", "4773Z");

// ARS territorialement compétente
define("ARS_COORD", "ARS Bretagne - 6 pl. des Colombes - CS 14253 - 35042 Rennes cedex - +33 (0)2 90 08 80 00");
define("ARS_URL", "https://www.bretagne.ars.sante.fr/");

define("ARS_COORD", "ARS des Pays de la Loire - 17 bd Gaston Doumergue - CS 56233 - 44262 Nantes cedex 2 - 02 49 10 40 00");
define("ARS_URL", "https://www.pays-de-la-loire.ars.sante.fr/");

// Hébergeur du site
define("HBG_COORD", "OVH - 2 rue Kellermann - BP 80157 - 59053 Roubaix cedex 1");
define("HBG_URL", "https://www.ovh.com/fr/");

// Agence Nationale de Sécurité du Médicament et des Produits de Santé
define("ANSM_COORD", "ANSM - 143 / 147 bd Anatole France - 93285 St-Denis cedex - +33 (0)1 55 87 30 00");
define("ANSM_URL", "http://ansm.sante.fr/");

// Ordre National des Pharmaciens
define("ORDRE_COORD", "Ordre national des pharmaciens - 12 rue Ampère - 75017 Paris - +33 (0)1 56 21 34 34");
define("ORDRE_URL", "http://www.ordre.pharmacien.fr/");

// Ministère en charge de la santé
define("MINIS_COORD", "Ministère des solidarités et de la santé - 14 av. Duquesne - 75350 Paris 07 SP - +33 (0)1 40 56 60 00");
define("MINIS_URL", "http://solidarites-sante.gouv.fr/");

// Pour la VMI :
// - chaque page comportera le logo commun mis en place au niveau communautaire et défini selon la directive européenne
// - un élément sélectionnable, parfaitement identifié, permettra d'accéder au service de vente de médicaments.
//   (les médicaments y seront présentés de manière claire, objective et non trompeuse)


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
define("LABEL_EXP_PIRATE", "Site pharmacie - Attention");

// adresse du site de la pharmacie :
define("ADRESSE_SITE_PHARMACIE", "http://bigouig.fr/");
define("S_ADRESSE_SITE_PHARMACIE", "https://bigouig.fr/");
define("W_ADRESSE_SITE_PHARMACIE", "http://www.bigouig.fr/");
define("SW_ADRESSE_SITE_PHARMACIE", "https://www.bigouig.fr/");

// adresse mail de la pharmacie :
define("MAIL_DEST_PHARMA", "clr.tstph@use.startmail.com");

// pour vérifier les 1ers mails officiels :
//	define("MAIL_DEST_CLR",    "clr.tstph@use.startmail.com");

// taille max de la pièce jointe : 5 Mo = 5242880 octets
define("TAILLE_MAX_PJ", 5242880);

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
define("NB_CAR_MIN_HTM", 1);
define("NB_CAR_MAX_HTM", 45);

// Nombre de caractères min et max pour le mot de passe :
define("NB_CAR_MIN_MDP", 5);
define("NB_CAR_MAX_MDP", 20);
define("NB_CAR_MIN_MDP_HTM", 2);
define("NB_CAR_MAX_MDP_HTM", 25);

// Nombre de caractères min et max pour le texte libre :
define("NB_CAR_MIN_MESSAGE", 5);
define("NB_CAR_MAX_MESSAGE", 1000);
define("NB_CAR_MIN_MESSAGE_HTM", 1);
define("NB_CAR_MAX_MESSAGE_HTM", 1000);

//////////////////////////////////////////////////////////////////////////////////////////
///////////////                                                            ///////////////
///////////////           Horaires d'ouverture de la pharmacie             ///////////////
///////////////                                                            ///////////////
///////////////           (ctes utilisées dans fonctions.php)              ///////////////
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

// Pour les medias braille, on remplace tout le 'tableau' précédent par une simple phrase :
define('HORAIRES_PHARMACIE',
    "La pharmacie est ouverte du lundi au vendredi de 8h30 à 12h30 et de 14h à 19h30, et le samedi de 9h à 16h.");

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

//////////////////////////////////////////////////////////////////////////////////////////
///////////////                                                            ///////////////
///////////////                          Contact                           ///////////////
///////////////                                                            ///////////////
///////////////                     (infos pratiques)                      ///////////////
///////////////                                                            ///////////////
//////////////////////////////////////////////////////////////////////////////////////////

// lignes de bus / tram / métro, parking privé, ...
define('CONTACT_INFOS_PRATIQUES',
    "<p>La pharmacie dispose d'un parking pour sa clientèle.</p>
    <p>En <span>chronobus C6</span>, descendez à l'arrêt <span>St Joseph de Porterie</span> :
         la pharmacie est alors à moins d'une minute.</p>");

?>