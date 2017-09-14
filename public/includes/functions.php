<?php

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
	//						testCaptcha()
	//
	// Fonction qui sert à la validation du captcha de Google.
	// Elle est déclenchée lors du clic sur le bouton d'envoi du formulaire.
	// Elle retourne la réponse de Google : captcha validé ou non.
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	function testCaptcha($user_response) {
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
	//						superTrim()
	//
	// Fonction qui prolonge l'action de la fonction php trim().
	// trim() supprime les espaces à gauche et à droite de la chaine, mais ici, en plus :
	// - on transforme les tabulations en espace
	// - on transforme les multiples occurrences d’espace en 1 seul espace
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	function superTrim($string)
	{
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
	function dateFr(){
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
	//					heureActuelle()
	//
	// Fonction qui donne l'heure actuelle sous 2 formats possible, selon le paramètre $format
	//
	// - "heure,minutes" en décimal après la virgule       <--- par défaut
	//
	// - "heure h minutes" en format horaire classique     <--- $format = 'H'
	//
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	function heureActuelle($format){
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
	//						pharmacieOuverte()
	//
	// Fonction qui donne l'état d'ouverture de la pharmacie :
	//
	// - la pharmacie est ouverte
	// - la pharmacie est fermée
	// - la pharmacie ouvre dans X mn
	// - la pharmacie ferme dans X mn
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	function pharmacieOuverte(){
		define("OUV_MAT", 8.5);
		define("FER_MID", 12.5);
		define("OUV_AMI", 14);    // horaires au format DECIMAL
		define("FER_SOI", 19.5);
		define("OUV_SAM", 9);
		define("FER_SAM", 16);
		define("REBOURS", 0.25);  // compte à rebours    0.25 = 15 mn

		$heureActuelle = heureActuelle(''); // format décimal demandé

		if( date('D') != "Sat" ){
			// on est un jour de semaine hors samedi :
			if( ($heureActuelle <= (OUV_MAT - 0.25)) || ($heureActuelle >= FER_SOI) ){
				return "La pharmacie est actuellement fermée.";
			}
			else if( $heureActuelle < OUV_MAT ){
				return "Patience, la pharmacie ouvre dans moins de " . ceil( ceil( (OUV_MAT - $heureActuelle) * 60 ) / 5 ) * 5 . " minutes.";
			}
			else if( ($heureActuelle >= OUV_MAT) && ($heureActuelle <= (FER_MID - 0.25)) ){
				return "La pharmacie est actuellement ouverte.";
			}
			else if( $heureActuelle < FER_MID ){
				return "Hâtez-vous, la pharmacie ferme dans moins de " . ceil( ceil( (FER_MID - $heureActuelle) * 60 ) / 5 ) * 5 . " minutes.";
			}
			else if( ($heureActuelle >= FER_MID) && ($heureActuelle <= (OUV_AMI - 0.25)) ){
				return "C'est la pause déjeuner, la pharmacie est actuellement fermée.";
			}
			else if( $heureActuelle < OUV_AMI ){
				return "Patience, la pharmacie ré-ouvre dans moins de " . ceil( ceil( (OUV_AMI - $heureActuelle) * 60 ) / 5 ) * 5 . " minutes.";
			}
			else if( ($heureActuelle >= OUV_AMI) && ($heureActuelle <= (FER_SOI - 0.25)) ){
				return "La pharmacie est actuellement ouverte.";
			}
			else if( $heureActuelle < FER_SOI ){
				return "Hâtez-vous, la pharmacie ferme dans moins de " . ceil( ceil( (FER_SOI - $heureActuelle) * 60 ) / 5 ) * 5 . " minutes.";
			}
		}
		else{
			// on est un samedi :
			if( ($heureActuelle <= OUV_SAM - 0.25) || ($heureActuelle >= FER_SAM) ){
				return "La pharmacie est actuellement fermée.";
			}
			else if( $heureActuelle < OUV_SAM ){
				return "Patience, la pharmacie ouvre dans moins de " . ceil( ceil( (OUV_SAM - $heureActuelle) * 60 ) / 5 ) * 5 . " minutes.";
			}
			else if( ($heureActuelle >= OUV_SAM) && ($heureActuelle <= (FER_SAM - 0.25)) ){
				return "La pharmacie est actuellement ouverte.";
			}
			else if( $heureActuelle < FER_SAM ){
				return "Hâtez-vous, la pharmacie ferme dans moins de " . ceil( ceil( (FER_SAM - $heureActuelle) * 60 ) / 5 ) * 5 . " minutes.";
			}
		}
	}

?>