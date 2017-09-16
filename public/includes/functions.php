<?php

///////////////////////////////////////////////////////////////////////////////////////////////
//
//						integreCLR( $nomDuFichier )
//
// NB: inclusion de fichiers '.php' uniquement
//
// Pour inclure de façon sécurisée un fichier (notamment 'functions.php') en tête
// d'un fichier PHP, ou HTML, ... , il faut écrire tout un paragraphe assez lourd.
//
// Mais une fois 'functions.php' inclus, si on a besoin d'inclure d'autres fichiers,
// il est alors très utile d'utiliser cette fonction 'integreCLR()' !
//
// Pour compliquer un peu l'opération, il faut ajouter '_CLRX' au vrai nom du fichier
// à inclure. Cette 'extension' sera alors ensuite supprimée dans le traitement
// de la fonction. Le 'X' est la 4e lettre du nom du fichier à inclure.
//
///////////////////////////////////////////////////////////////////////////////////////////////
function integreCLR( $nomDuFichier ){
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
	    if (file_exists("includes/" . $page) && $page != 'index.php') {
	    	include_once("./includes/".$page);
	    }
	    else{
	    	echo "Erreur Include : le fichier " . $page . " est introuvable.";
	    }
	}
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
		return $_SERVER['HTTP_CLIENT_IP'];
	}
	// IP derrière un proxy
	elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	// Sinon : IP normale
	else {
		return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
	}
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
//						pharmacieOuverte( $jour, $heure )
//
// Fonction qui donne l'état d'ouverture de la pharmacie à l'heure passée en paramètre
//
// - la pharmacie est ouverte
// - la pharmacie est fermée
// - la pharmacie ouvre dans X mn
// - la pharmacie ferme dans X mn
//
///////////////////////////////////////////////////////////////////////////////////////////////
function pharmacieOuverte( $jour, $heure ) {

	if( $jour != "sam" ){
		// on est un jour de semaine hors samedi :
		if( ($heure < (OMATD - REBOURSD)) || ($heure >= FAMID) ){
			return "La pharmacie est actuellement <span class='ferme'>fermée</span>."; // les <span> ne sont pris en compte en CSS que pour la page index
		}
		else if( $heure < OMATD ){
			return "Patience, la pharmacie ouvre dans <span class='ouve'>moins de " . ceil( ceil( (OMATD - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
		else if( ($heure >= OMATD) && ($heure < (FMATD - REBOURSD)) ){
			return "La pharmacie est actuellement <span class='ouvert'>ouverte</span>.";
		}
		else if( $heure < FMATD ){
			return "Hâtez-vous, la pharmacie ferme dans <span class='fer'>moins de " . ceil( ceil( (FMATD - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
		else if( ($heure >= FMATD) && ($heure < (OAMID - REBOURSD)) ){
			return "C'est la pause déjeuner, la pharmacie est actuellement <span class='ferme'>fermée</span>.";
		}
		else if( $heure < OAMID ){
			return "Patience, la pharmacie ré-ouvre dans <span class='ouve'>moins de " . ceil( ceil( (OAMID - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
		else if( ($heure >= OAMID) && ($heure < (FAMID - REBOURSD)) ){
			return "La pharmacie est actuellement <span class='ouvert'>ouverte</span>.";
		}
		else if( $heure < FAMID ){
			return "Hâtez-vous, la pharmacie ferme dans <span class='fer'>moins de " . ceil( ceil( (FAMID - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
	}
	else{
		// on est un samedi :
		if( ($heure < (SA_OMATD - REBOURSD)) || ($heure >= SA_FAMID) ){
			return "La pharmacie est actuellement <span class='ferme'>fermée</span>.";
		}
		else if( $heure < SA_OMATD ){
			return "Patience, la pharmacie ouvre dans <span class='ouve'>moins de " . ceil( ceil( (SA_OMATD - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
		else if( ($heure >= SA_OMATD) && ($heure < (SA_FAMID - REBOURSD)) ){
			return "La pharmacie est actuellement <span class='ouvert'>ouverte</span>.";
		}
		else if( $heure < SA_FAMID ){
			return "Hâtez-vous, la pharmacie ferme dans <span class='fer'>moins de " . ceil( ceil( (SA_FAMID - $heure) * 60 ) / PAS_DE_REBOURS ) * PAS_DE_REBOURS . " minutes</span>.";
		}
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////
//
//							getDeltaP( $heure )
//
// Cette fonction prend comme paramètre d'entrée l'heure actuelle au format DECIMAL.
//
// Elle renvoie ensuite 2 valeurs, rangées dans un tableau :
// - la 1ère est le % dont il faut décaler (left: ) la div du trait vertical (id #trait),
//   représentant l'heure actuelle
//
// - la 2ème, au format BOOLEEN, indique s'il faut afficher le trait ou non.
//   (on affiche le trait entre 8h30 et 19h30, sinon on n'affiche pas le trait)
//
//		>>>   ATTENTION   <<<
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

		$dessinerTrait = true; // pour afficher l'id 'trait' dans le div du jour

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
		$dessinerTrait = false; // pour afficher la classe 'effacerTrait' quand on est avant 8h30 ou après 19h30
	}

	return [$deltaP, $dessinerTrait];
}

?>