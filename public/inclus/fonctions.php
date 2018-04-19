<?php

require_once("constantes.php");

ini_set("display_errors", 1);  // affichage des erreurs - à virer à la mise en prod !

///////////////////////////////////////////////////////////////////////////////////////////////
//
//						require_onceCLR( $nomDuFichier )
//
// NB: inclusion de fichiers '.php' uniquement, dans le répertoire 'inclus' obligatoirement
//
// Pour inclure de façon sécurisée un fichier (notamment 'fonctions.php') en tête
// d'un fichier PHP, ou HTML, ... , il faut écrire tout un paragraphe assez lourd.
//
// Mais une fois 'fonctions.php' inclus, si on a besoin d'inclure d'autres fichiers,
// il est alors très utile d'utiliser cette fonction 'require_onceCLR()' !
//
// Pour compliquer un peu l'opération, il faut ajouter '_CLRX' au vrai nom du fichier
// à inclure. Cette 'extension' sera alors ensuite supprimée dans le traitement
// de la fonction. Le 'X' est la 4e lettre du nom du fichier à inclure.
//
///////////////////////////////////////////////////////////////////////////////////////////////
function require_onceCLR( $nomDuFichier ){
	if( empty($page) ){
		$page = $nomDuFichier;

		// On construit le nom de la page à inclure en prenant qqs précautions :
		$page = trim( $page );					// on supprime d'éventuels espaces en début et fin de chaîne
		$car1 = ucfirst(substr($page, 3, 1));	// on extrait le 4e caractère du nom du fichier à inclure
		$suff = '_CLR' . $car1;					// on construit le suffixe à enlever
		$page = str_replace($suff, '', $page);	// on enlève le suffixe
		$page .= ".php";						// ajout dynamique de l'extension .php
	}

	// On remplace les caractères qui permettent de naviguer dans les répertoires
	$page = str_replace("../","protect",$page);
	$page = str_replace(";","protect",$page);
	$page = str_replace("%","protect",$page);

	// On interdit l'inclusion de dossiers protégés par htaccess.
	if( strpos($page, 'admin') ){
		echo "Vous n'avez pas accès à ce répertoire";
	}
	else{
	    // On vérifie que la page est bien sur le serveur
	    if (file_exists("HOME"."www/inclus/".$page) && $page != "index.php") {
	    	require_once("HOME"."www/inclus/".$page);
	    }
	    else{
	    	echo "Erreur require_onceCLR : le fichier " . $page . " est introuvable.";
	    }
	}
}

// If your code is running on multiple servers with different environments (locations from where your scripts run) the following idea may be useful to you:

// a. Do not give absolute path to include files on your server.
// b. Dynamically calculate the full path (absolute path)

// Hints:
// Use a combination of dirname(__FILE__) and subsequent calls to itself until you reach to the home of your '/index.php'. Then, attach this variable (that contains the path) to your included files.

// One of my typical example is:

// <?php
// define('__ROOT__', dirname(dirname(__FILE__)));
// require_once(__ROOT__.'/config.php');
//

// instead of:
// <?php require_once('/var/www/public_html/config.php');

// After this, if you copy paste your codes to another servers, it will still run, without requiring any further re-configurations.


///////////////////////////////////////////////////////////////////////////////////////////////
//
//							Quelques variables globales
//
//		destinées aux 2 fonctions de remplacement de caractères ci-dessous
//		(  filtrerPrenom() et filtrerNom()  )
//
// - la 1ère utilise    str_replace()
//
// 		=>	chaque caractère du tableau $trouverCar sera remplacé par son équivalent
// 			(même indice) dans le tableau $nouveauCar.
// 			Quand il n'y a pas de correspondance pour un caractère de $trouverCar dans $nouveauCar,
// 			ce qui est le cas pour tous les caractères sauf le _ et le ", str_replace le remplace
// 			par le caractère vide : ''.
//
//
// 		A FAIRE éventuellement :
// 			utilisation des expressions régulières : remplacer tout ce qui n'est pas dans la liste par ''                       +++++++++
// 			et la liste serait constituée de a-z, A-Z, -, âäàêëéèîïì ... ñ
//
//
//
// - la 2ème utilise en plus    strtr()     (équivalente en temps à str_replace())
//
// 		=>	ici on a directement dans 1 seul tableau, $minusAccMajus, un caractère
// 			et son remplaçant
//
///////////////////////////////////////////////////////////////////////////////////////////////

$trouverCar =
['_', '"', '²', '&', '~', '#', '{', '}', '[', ']', '|', '`', '^', '@', '(', ')', '°', '=',
 '+', '€', '¨', '^', '$', '£', '¤', '%', '*', 'µ', '?', ',', ';', ':', '!', '§', '<', '>', '/', '\\',
 '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.'];

$nouveauCar = [' ', "'"];

$minusAccMajus =
['â' => 'Â', 'ä' => 'Ä', 'à' => 'À',
 'ê' => 'Ê', 'ë' => 'Ë', 'è' => 'È', 'é' => 'É', 
 'î' => 'Î', 'ï' => 'Ï', 'ì' => 'Ì',
 'ô' => 'Ô', 'ö' => 'Ö', 'ò' => 'Ò',
 'û' => 'Û', 'ü' => 'Ü', 'ù' => 'Ù',
 'ç' => 'Ç', 'ñ' => 'Ñ'];

///////////////////////////////////////////////////////////////////////////////////////////////
//
//						filtrerPrenom()
//
// Fonction qui sert à rendre le prénom "inoffensif et joli" :
//
// - inoffensif : du point de vue de la sécurité, on essaie de supprimer les codes néfastes
//
// - joli : première lettre en majuscule, puis minuscules
//          (et on autorise les tirets -   et les apostrophes simples ')
//
// Elle prend en entrée la variable directement issue du formulaire : $_POST['prenom']
//
// Elle renvoie en sortie le prénom, "nettoyé" si besoin, ainsi qu'une erreur éventuelle
// 
///////////////////////////////////////////////////////////////////////////////////////////////
function filtrerPrenom($prenomPOST) {

	// on commence par donner à la fonction filtrerPrenom() la
	// connaissance des 2 variables $trouverCar et $nouveauCar :
	global $trouverCar, $nouveauCar;

	// longueur de la chaîne initiale
	$nbCar = strlen($prenomPOST);

	// supprime les balises HTML et PHP :
	// (avant c'était dans le else ci-dessous, mais en cas de $nbCar invalide,
	//  le champ était vide lors du rechargement de la page ... c'était bof !)
	$prenom = strip_tags($prenomPOST);

	// test de la contrainte sur la longueur de la chaîne :
	if( ($nbCar < NB_CAR_MIN) || ($nbCar > NB_CAR_MAX) ){
		$erreur = "(entre " . NB_CAR_MIN . " et " . NB_CAR_MAX . " caractères)";
	}
	else{
		// cf explications sur le remplacement de car. ci-dessus :
		$prenom = str_replace($trouverCar, $nouveauCar, $prenom);
		// enlève les espaces de début, fin, et les double-espaces en milieu de chaîne :
		$prenom = superTrim($prenom);
		// 1ère lettre de chaque mot (délimités par un tiret '-', un espace ' ' ou une tabulation)
		// en majuscule, les autres en minuscules :
		$prenom = ucwords(strtolower($prenom), "- \t");
		// on informe l'utilisateur en cas de modif de sa saisie :
		if( $prenom != $prenomPOST ){
			$erreur = "(orthographe modifiée => veuillez revalider)";
		}
	}
	return [$prenom, $erreur];
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//						filtrerNom()
//
// Fonction qui sert à rendre le nom "inoffensif et joli" :
//
// - inoffensif : du point de vue de la sécurité, on essaie de supprimer les codes néfastes
//
// - joli : toutes les lettres en majuscule, même les lettres accentuées
//          (et on autorise les tirets -   et les apostrophes simples ')
//
// Elle prend en entrée la variable directement issue du formulaire : $_POST['nom']
//
// Elle renvoie en sortie le nom, "nettoyé" si besoin, ainsi qu'une erreur éventuelle
//
///////////////////////////////////////////////////////////////////////////////////////////////
function filtrerNom($nomPOST) {

	global $trouverCar, $nouveauCar, $minusAccMajus;
	$nbCar = strlen($nomPOST);
	$nom = strip_tags($nomPOST);

	if( ($nbCar < NB_CAR_MIN) || ($nbCar > NB_CAR_MAX) ){
		$erreur = "(entre " . NB_CAR_MIN . " et " . NB_CAR_MAX . " caractères)";
	}
	else{
		$nom = str_replace($trouverCar, $nouveauCar, $nom);
		$nom = superTrim($nom);
		$nom = strtoupper($nom);
		$nom = strtr($nom, $minusAccMajus);
		if( $nom != $nomPOST ){
			$erreur = "(orthographe modifiée => veuillez revalider)";
		}
	}
	return [$nom, $erreur];
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//						getIpAdr()
//
// Fonction qui sert à obtenir l'adresse IP du client
//
///////////////////////////////////////////////////////////////////////////////////////////////
function getIpAdr() {
	// IP si internet partagé
	if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	// IP derrière un proxy
	elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	// Sinon : IP normale
	else {
		$ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
	}
	return $ip;
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//						testCaptcha( $user_response )
//
// Fonction qui sert à la validation du captcha de Google.
// Elle est déclenchée lors du clic sur le bouton d'envoi du formulaire.
// Elle retourne la réponse de Google : captcha validé ou non.
//
///////////////////////////////////////////////////////////////////////////////////////////////
function testCaptcha( $user_response ) {
    $fields_string = '';
    $fields = array(
        'secret' => '6LcPQyUUAAAAAFVpINP0NVsIu80r7V-CrBEkW8tL',
        'response' => $user_response
    );
    foreach($fields as $key=>$value)
    $fields_string .= $key . '=' . $value . '&';
    $fields_string = rtrim($fields_string, '&');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);

    $result = curl_exec($ch);
    curl_close($ch);

print_r(json_decode($result, true));

    return json_decode($result, true);
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//						superTrim( $string )
//
// Fonction qui prolonge l'action de la fonction php trim().
// trim() supprime les espaces à gauche et à droite de la chaine, mais ici, en plus :
// - on transforme les tabulations en espace
// - on transforme les multiples occurrences d’espace en 1 seul espace
//
///////////////////////////////////////////////////////////////////////////////////////////////
function superTrim( $string ) {
	$string = trim($string);
	$string = str_replace('\t', ' ',  $string);
	$string = preg_replace('/[ ]+/', ' ',  $string);
	return $string;      	
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//						dateFr()
//
// Fonction qui donne la date du jour en français :      vendredi 2 juillet 2017
//
// Comme le site est hébergé sur un serveur mutualisé, on est obligé de partager la 
// configuration du serveur avec tout le monde, et on ne peut donc pas le paramétrer
// à notre guise :   setlocale(LC_TIME, 'fra')   n'a donc aucun effet :-(
//
// On ne peut donc pas bénéficier de la fonction strftime() qui nous aurait tout bien affiché.
//
// C'est pourquoi on définit notre propre fonction ici.
//
///////////////////////////////////////////////////////////////////////////////////////////////
function dateFr() {
	$jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
	$mois  = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
	date_default_timezone_set("Europe/Paris");
	$jour = date('j');
	$jour .= ( $jour == 1 ) ? "er" : ""; // on ajoute 'er' pour chaque 1er du mois
	$date = $jours[date('N')-1] . " " . $jour . " " . $mois[date('n')-1] . " " . date('Y');
	return $date;
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//					heureActuelle( $format )
//
// Fonction qui donne l'heure actuelle sous 2 formats possible, selon le paramètre $format
//
// - "heure,minutes" en décimal après la virgule       <--- par défaut
//
// - "heure h minutes" en format horaire classique     <--- $format = 'H'
//
//
///////////////////////////////////////////////////////////////////////////////////////////////
function heureActuelle( $format ) {
	date_default_timezone_set("Europe/Paris");
	if( $format == 'H' ){
		$heure = date('G\hi');
	}
	else{
		$heure = (int)date('G') + round( (float)date('i') / 60, 2 ); // heure au format DECIMAL :    heure (0 -> 23) + ( minutes (00 -> 59) / 60 )
	}
	return $heure;
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//						ouverturePharmacie( $jour, $heure )
//
// Fonction qui donne l'état d'ouverture de la pharmacie à l'heure passée en paramètre
// (utilisée par index.php et horaires.php)
//
// - la pharmacie est ouverte
// - la pharmacie est fermée
// - la pharmacie ouvre dans X mn
// - la pharmacie ferme dans X mn
//
///////////////////////////////////////////////////////////////////////////////////////////////
function ouverturePharmacie( $jour, $heure ) {

	if( $jour != "sam" && $jour != "dim" ){
		// on est un jour de semaine hors samedi :
		if( ($heure < (OMATD - REBOURSD)) || ($heure >= FAMID) ){
			$infoOuverture = "La pharmacie est actuellement <span class='cFermee'>fermée</span>."; // les <span> ne sont pris en compte en CSS que pour la page index
		}
		else if( $heure < OMATD ){
			$infoOuverture = "Patience, la pharmacie ouvre dans <span class='cOuvreDans'>moins de " . ceil( ceil( (OMATD - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
		else if( ($heure >= OMATD) && ($heure < (FMATD - REBOURSD)) ){
			$infoOuverture = "La pharmacie est actuellement <span class='cOuverte'>ouverte</span>.";
		}
		else if( $heure < FMATD ){
			$infoOuverture = "Hâtez-vous, la pharmacie ferme dans <span class='cFermeDans'>moins de " . ceil( ceil( (FMATD - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
		else if( ($heure >= FMATD) && ($heure < (OAMID - REBOURSD)) ){
			$infoOuverture = "C'est la pause déjeuner, la pharmacie est actuellement <span class='cFermee'>fermée</span>.";
		}
		else if( $heure < OAMID ){
			$infoOuverture = "Patience, la pharmacie ré-ouvre dans <span class='cOuvreDans'>moins de " . ceil( ceil( (OAMID - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
		else if( ($heure >= OAMID) && ($heure < (FAMID - REBOURSD)) ){
			$infoOuverture = "La pharmacie est actuellement <span class='cOuverte'>ouverte</span>.";
		}
		else if( $heure < FAMID ){
			$infoOuverture = "Hâtez-vous, la pharmacie ferme dans <span class='cFermeDans'>moins de " . ceil( ceil( (FAMID - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
	}
	else if( $jour == "sam" ){
		// on est un samedi :
		if( ($heure < (SA_OMATD - REBOURSD)) || ($heure >= SA_FAMID) ){
			$infoOuverture = "La pharmacie est actuellement <span class='cFermee'>fermée</span>.";
		}
		else if( $heure < SA_OMATD ){
			$infoOuverture = "Patience, la pharmacie ouvre dans <span class='cOuvreDans'>moins de " . ceil( ceil( (SA_OMATD - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
		else if( ($heure >= SA_OMATD) && ($heure < (SA_FAMID - REBOURSD)) ){
			$infoOuverture = "La pharmacie est actuellement <span class='cOuverte'>ouverte</span>.";
		}
		else if( $heure < SA_FAMID ){
			$infoOuverture = "Hâtez-vous, la pharmacie ferme dans <span class='cFermeDans'>moins de " . ceil( ceil( (SA_FAMID - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
	}
	else{
		// on est un dimanche :
		$infoOuverture = "La pharmacie est actuellement <span class='cFermee'>fermée</span>, nous vous souhaitons un bon week-end !";
	}
	return $infoOuverture;
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//							getDeltaP( $heure )
//
// Cette fonction, utilisée par horaires.php, détermine 2 valeurs
// destinées à l'affichage dynamique du curseur de l'heure actuelle.
//
// La fonction prend comme paramètre d'entrée l'heure actuelle au format DECIMAL.
//
// Elle renvoie donc 2 valeurs, rangées dans un tableau :
// - la 1ère est le % dont il faut décaler (left: ) la div du trait vertical (id #iTraitHoraire),
//   représentant l'heure actuelle
//
// - la 2ème, au format BOOLEEN, indique s'il faut afficher le trait ou non.
//   (on affiche le trait entre 8h30 et 19h30, sinon on n'affiche pas le trait)
//
//
//
//		          >>>   ATTENTION   <<<
//
//
//
// Ceci implique de ne rien changer, dans le fichier CSS, aux valeurs de width,
// de padding left ou right du § intitulé :
//
//				"largeurs et marges des jours et des créneaux horaires"
//
///////////////////////////////////////////////////////////////////////////////////////////////
function getDeltaP( $heure ) {

	// 8h30 = référence 0 pour le trait vertical, même pour le samedi.
	// côté css, pour que le trait vertical soit à 8h30, il faut le décaler de la largeur du jour de la semaine, soit 23%.
	// et la journée de 8h30 à 19h30 se décompose ainsi :
	// 8h30  -> 12h30 : 4h   couverts par 14 + 14 = 28%  +  1 % de padding-left  +  1 % de padding-right  =  30 % en tout
	// 12h30 -> 14h   : 1h30 couverts par 5%
	// 14h   -> 19h30 : 5h30 couverts par 19 + 19 = 38%  +  1 % de padding-left  +  1 % de padding-right  =  40 % en tout

	if( $heure >= OMATD && $heure < FAMID){

		$dessinerTrait = true; // pour afficher l'id 'iTraitHoraire' dans le div du jour

		// le nom 'deltaP' est sensé évoquer 'delta %'
		$deltaP = 23; // 23%, auxquels on va rajouter des % en fonction du créneau horaire

		if( $heure < FMATD ){

			// les 4h de la matinée sont représentées par une width de 30% en CSS :
			$deltaP += ($heure - OMATD) / (FMATD - OMATD) * 30;

		}else if( $heure >= FMATD && $heure < OAMID ){

			// les 1h30 de la pause déjeuner (1.5 en décimal) sont représentées par une width de 5% :
			$deltaP += 30 + ($heure - FMATD) / (OAMID - FMATD) * 5;

		}else if( $heure >= OAMID && $heure < FAMID ){

			// les 5h30 de l'après-midi (5.5 en décimal) sont représentées par une width de 40% :
			$deltaP += 30 + 5 + ($heure - OAMID) / (FAMID - OAMID) * 40;
		}
	}
	else{
		$deltaP = 0; // en fait, on n'a pas besoin de deltaP dans ce cas, mais pour éviter un message d'erreur, on le met à 0
		$dessinerTrait = false; // pour afficher la classe 'cEffacerTrait' quand on est avant 8h30 ou après 19h30
	}

	return [$deltaP, $dessinerTrait];
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//							pageCourante( $request_uri )
//
// Cette fonction, utilisée en tête de chaque fichier du site, a 2 objectifs :
//
// - souligner, dans le menu de navigation, le mot correspondant à la page active
//   (si c'est une des 4 du menu de navigation)
//
// - ajouter au 'title' de chaque page du site le nom de cette page
//
// La fonction prend comme paramètre d'entrée la page courante obtenue par
// la super globale   $_SERVER['REQUEST_URI']
//
// Elle renvoie un tableau de 2 éléments :
//
// - une chaîne de 4 caractères dont 1 seul est positionné à '1' :
//   celui correspondant à la page courante du menu de navigation, dans cet ordre :
//   index / horaires / équipe / contact
//   (dans le but de souligner ce mot)
//
// - le nom 'enjolivé' de la page :
//       ex. 'Accueil' à la place de '/index.php'
//
///////////////////////////////////////////////////////////////////////////////////////////////
function pageCourante( $request_uri ) {

	// on extrait le nom de la page courante :
	$page = ltrim($request_uri, '/');

	// on enlève l'extension '.php' :
	$page = rtrim($page, 'hp.');

	// on initialise la chaîne de la page courante à 4 x '0' en respectant
	// l'ordre suivant : Index / Horaires / Equipe / Contact :
	$flagPC = "0000";

	// on positionne alors à '1' le digit de la page courante
	switch( $page ){
		case "index":
			$flagPC = "1000";
			$nomPage = "Accueil";
			break;

		case "horaires":
			$flagPC = "0100";
			$nomPage = "Horaires";
			break;

		case "equipe":
			$flagPC = "0010";
			$nomPage = "Équipe";
			break;

		case "contact":
			$flagPC = "0001";
			$nomPage = "Contact";
			break;

		case "connexion":
			$nomPage = "Connexion";
			break;

		case "inscription":
			$nomPage = "Inscription";
			break;

		case "prepaOrdonnance":
			$nomPage = "Préparation d'ordonnance";
			break;

		case "prepaCommande":
			$nomPage = "Préparation de commande";
			break;

		case "pharmaDeGarde":
			$nomPage = "Pharmacies de garde";
			break;

		case "promos":
			$nomPage = "Promotions";
			break;

		case "gammesProduits":
			$nomPage = "Gammes de produits";
			break;

		case "infos":
			$nomPage = "Informations / conseils";
			break;
	}
	return ['flag' => $flagPC, 'nom' => $nomPage];
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//							racClavier( $http_user_agent )
//
// Cette fonction, utilisée par aide.php, a pour but d'indiquer à l'utilisateur la bonne
// combinaison de touches pour les raccourcis clavier.
// (cette combinaison dépend de la configuration (OS + navigateur) du client)
//
// La fonction prend comme paramètre d'entrée la super globale $http_user_agent
//
// Elle renvoie un tableau de 2 éléments :
//
// - une chaîne de caractères décrivant le navigateur, l'OS et la combinaison de
// touches à utiliser, sous forme d'une phrase
//
// - une chaîne de caractères toute simple donnant la combinaison de touches, ex.: 'ALT'
//
///////////////////////////////////////////////////////////////////////////////////////////////
function racClavier( $http_user_agent ) {

	// Dans un 1er temps, on identifie le navigateur :

	// voici qqs ex. de la valeur de $_SERVER['HTTP_USER_AGENT'] :

	// MAC Firefox	=> Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:58.0) GecKo/20100101 Firefox/58.0
	// W8  Firefox	=> Mozilla/5.0 (Windows NT 6.2; Win64; x64;      rv:58.0) Gecko/20100101 Firefox/58.0

	// MAC Chrome	=> Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.146 Safari/537.36
	// W8  Chrome	=> Mozilla/5.0 (Windows NT 6.2; Win64; x64)        AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.146 Safari/537.36

	// MAC Safari	=> Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.5.6 (KHTML, like Gecko) Version/11.0.3 Safari/604.5.6

	// MAC Opera	=> Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36 OPR/51.0.2830.55

	// W8  Lynx		=> Lynx/2.8.9dev.17 libwww-FM/2.14FM SSL-MM/1.4.1 OpenSSL/1.0.2o


	// d'où l'importance de l'ORDRE des tests ci-desssous !!!

	if( strpos($http_user_agent, 'MSIE') !== FALSE ||
	    strpos($http_user_agent, 'Trident') !== FALSE ||
	    strpos($http_user_agent, 'Edge') !== FALSE ){
	    $nav = "MSIE";
	    $phrase = "Dans le cas actuel (Internet Explorer / ";
	}
	elseif( strpos($http_user_agent, 'irefox') !== FALSE ){
	    $nav = "MZFF";
	    $phrase = "Dans le cas actuel (Firefox / ";
	}
	elseif( strpos($http_user_agent, 'OPR') !== FALSE ){
	    $nav = "OPE15";
	    $phrase = "Dans le cas actuel (Opera / ";                              // Attention : on ne sait pas différencier Ope15 et Ope12   !!!
	}
	elseif( strpos($http_user_agent, 'afari') !== FALSE &&
			strpos($http_user_agent, 'hrome') == FALSE ){
	    $nav = "APSA";
	    $phrase = "Dans le cas actuel (Safari / ";
	}
	elseif( strpos($http_user_agent, 'hrome') !== FALSE ){
	    $nav = "GGCH";
	    $phrase = "Dans le cas actuel (Chrome / ";
	}
	elseif( strpos($http_user_agent, 'ynx') !== FALSE ){
	    $nav = "LYNX";
	    $phrase = "Dans le cas actuel (Lynx / ";
	}
	else{
		$nav = "XXXX";
		$phrase = "Dans le cas actuel (navigateur indéterminé / ";
	}

	// on identifie l'OS :
	if( strpos($http_user_agent, 'indows') !== FALSE ){
	    $os = "WIN";
	    $phrase .= "Windows) ";
	}
	elseif( strpos($http_user_agent, 'linux') !== FALSE ){
	    $os = "LIN";
	    $phrase .= "Linux) ";
	}
	elseif( strpos($http_user_agent, 'acintosh') !== FALSE ){
	    $os = "MAC";
	    $phrase .= "MacOS) ";
	}
	else{
	    $os = "";
	    $phrase .= "OS indéterminé) ";
	}

	// on complète le résultat par la combinaison des touches du raccourci clavier :
	switch ($os) {
	    case 'WIN':
	        switch ($nav) {
	            case 'GGCH':
	            case 'APSA':
	            case 'MSIE':
	            case 'OPE15':
	            	$combi = "ALT";
	                break;
	            case 'MZFF':
	            	$combi = "ALT + SHIFT";
	                break;
	            case 'OPE12':
	            	$combi = "SHIFT + ESC";
	                break;
	            case 'LYNX':
	            default:
	            	$combi = "";
	            }
	        break; 
	    case 'LIN':
	        switch ($nav) {
	            case 'GGCH':
	            	$combi = "ALT";
	                break;
	            case 'MZFF':
	            	$combi = "ALT + SHIFT";
	                break;
	            case 'APSA':
	            case 'MSIE':
	            case 'OPE15':
	            case 'OPE12':
	            case 'LYNX':
	            default:
	            	$combi = "";
	            }
	        break; 
	    case 'MAC':
	        switch ($nav) {
	            case 'GGCH':
	            case 'OPE15':
	            case 'APSA':
	            case 'MZFF':
	            	$combi = "CTRL + ALT";
	                break;
	            case 'MSIE':
	            case 'OPE12':
	            case 'LYNX':
	            default:
	            	$combi = "";
	            }
	        break;
	    default:
           	$combi = "";
	}

	if($combi != ""){
		$phrase .= "la combinaison est : " . $combi . " + touche(s) d'accès";
	}
	else{
		$phrase .= "les raccourcis clavier sont malheureusement indisponibles :-/";
	}

	return ['phrase' => $phrase, 'combi' => $combi];
}

?>