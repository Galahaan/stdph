<?php

	// Fonction qui sert à obtenir l'adresse IP du client
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

	// Fonction qui sert à la validation du captcha de Google.
	// Elle est déclenchée lors du clic sur le bouton d'envoi du formulaire.
	// Elle retourne la réponse de Google : captcha validé ou non.
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


	// Fonction qui prolonge l'action de la fonction php trim().
	// trim() supprime les espaces à gauche et à droite de la chaine, mais ici, en plus :
	// - on transforme les tabulations en espace
	// - on transforme les multiples occurrences d’espace en 1 seul espace
	function SuperTrim($string)
	{
		$string = trim($string);
		$string = str_replace('\t', ' ',  $string);
		$string = preg_replace('/[ ]+/', ' ',  $string);
		return $string;      	
	}

?>