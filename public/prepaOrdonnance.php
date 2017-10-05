<?php

	///////////////////////////////////////////////////////////////////////////////////////////////
	/////     INCLUDE sécurisé
	///////////////////////////////////////////////////////////////////////////////////////////////

	if( empty($page) ){
	$page = "functions"; // page à inclure : functions.php

	// On construit le nom de la page à inclure en prenant 2 précautions :
	// - ajout dynamique de l'extension .php
	// - on supprime également d'éventuels espaces en début et fin de chaîne
	$page = trim($page.".php");
	}

	// On remplace les caractères qui permettent de naviguer dans les répertoires
	$page = str_replace("../","protect",$page);
	$page = str_replace(";","protect",$page);
	$page = str_replace("%","protect",$page);

	// On interdit l'inclusion de dossiers protégés par htaccess.
	// S'il s'agit simplement de trouver la chaîne "admin" dans le nom de la page,
	// strpos() peut très bien le faire, et surtout plus vite !
	// if( preg_match('admin', $page) ){
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
	///////////////////////////////////////////////////////////////////////////////////////////////
	/////     FIN INCLUDE sécurisé
	///////////////////////////////////////////////////////////////////////////////////////////////

	integreCLR('constantes_CLRS');

	// Quelques constantes spécifiques à ce fichier :
	define("PAGE_EN_COURS", "prepaOrdonnance.php");

	// Si le formulaire vient d'être validé, et avant de savoir si on va envoyer le mail, on "nettoie" les champs :
	if( isset($_POST['bouton']) ){

		//  *******  CIVILITE  *******
		$civilite = $_POST['civilite'];

		// pour traiter le prénom et le nom, on va travailler un peu sur les chaînes de caractères :

		// Méthode de remplacement de caractères utilisant str_replace().
		// Chaque caractère du tableau $trouverCar sera remplacé par son équivalent
		// (même indice) dans le tableau $nouveauCar.
		// Quand il n'y a pas de correspondance pour un caractère de $trouverCar dans $nouveauCar,
		// ce qui est le cas pour tous les caractères sauf le '_', str_replace le remplace
		// par le caractère vide : ''.
		$trouverCar =
		['_', '²', '&', '~', '#', '"', "'", '{', '}', '[', ']', '|', '`', '^', '@', '(', ')', '°', '=',
		 '+', '€', '¨', '^', '$', '£', '¤', '%', '*', 'µ', '?', ',', ';', ':', '!', '§', '<', '>', '/', '\\',
		 '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.'];
		$nouveauCar = ['-'];

		// Méthode de remplacement de caractères utilisant strtr() équivalente en temps à str_replace(),
		// ici on a directement dans 1 seul tableau le remplaçant de chaque caractère :
		$minusAccMajus =
		['â' => 'Â', 'ä' => 'Ä', 'à' => 'À',
		 'ê' => 'Ê', 'ë' => 'Ë', 'è' => 'È', 'é' => 'É', 
		 'î' => 'Î', 'ï' => 'Ï', 'ì' => 'Ì',
		 'ô' => 'Ô', 'ö' => 'Ö', 'ò' => 'Ò',
		 'û' => 'Û', 'ü' => 'Ü', 'ù' => 'Ù',
		 'ç' => 'Ç', 'ñ' => 'Ñ'];

		// utilisation des expressions régulières : remplacer tout ce qui n'est pas dans la liste par ''                       +++++++++
		// et la liste serait constituée de a-z, A-Z, -, âäàêëéèîïì ... ñ


		//  ********  PRENOM  ********

		// supprime les balises HTML et PHP
		$prenom = strip_tags($_POST['prenom']);
		// cf explications sur le remplacement de car. ci-dessus
		$prenom = str_replace($trouverCar, $nouveauCar, $prenom);
		// enlève les espaces de début, fin, et les double-espaces en milieu de chaîne
		$prenom = superTrim($prenom);
		// remplace les espaces ' ' par des tirets '-'
		$prenom = str_replace(" ", "-", $prenom);
		// 1ère lettre en majuscule, les autres en minuscules
		$prenom = ucfirst(strtolower($prenom));
		// test de la contrainte sur la longueur de la chaîne
		if( (strlen($prenom) < NB_CAR_MIN) || (strlen($prenom) > NB_CAR_MAX ) ){
			$erreurs['prenom'] = "(entre " . NB_CAR_MIN . " et " . NB_CAR_MAX . " caractères)";
		}

		//  ********  NOM  ********

		$nom = strip_tags($_POST['nom']);
		$nom = str_replace($trouverCar, $nouveauCar, $nom);
		$nom = superTrim($nom);
		$nom = str_replace(" ", "-", trim($nom));
		// NOM en majuscule
		$nom = strtoupper($nom);
		$nom = strtr($nom, $minusAccMajus);

		if( (strlen($nom) < NB_CAR_MIN) || (strlen($nom) > NB_CAR_MAX ) ){
			$erreurs['nom'] = "(entre " . NB_CAR_MIN . " et " . NB_CAR_MAX . " caractères)";
		}

		//  ********  MAIL  ********

		// "nettoie" la valeur utilisateur :
		$adrMailClient = filter_var($_POST['adrMailClient'], FILTER_SANITIZE_EMAIL);

		// teste la NON validité du format :
		if( ! filter_var($adrMailClient, FILTER_VALIDATE_EMAIL) ){
			$erreurs['adrMailClient'] = "(format incorrect)"; 
		};

		//  ********  MESSAGE  ********

		// on traite volontairement le cas du message AVANT celui de la pièce jointe pour
		// ne conserver la pièce jointe QUE si AUCUNE erreur n'a été détectée dans les tests.
		// (chunk_split limite la longueur d'une ligne à 76 car. pour respecter la RFC 2045)
		$messageClientTxt = chunk_split(htmlspecialchars(strip_tags($_POST['message'])));
		if( (strlen($messageClientTxt) < NB_CAR_MIN_MESSAGE) || (strlen($messageClientTxt) > NB_CAR_MAX_MESSAGE ) ){
			$erreurs['message'] = "(entre " . NB_CAR_MIN_MESSAGE . " et " . NB_CAR_MAX_MESSAGE . " caractères)";
		}
		// on se donne une version du message en format HTML (plus sympa à lire pour la pharmacie)
		$messageClientHtml = "<b style=\"font-size: 16px;\">" . nl2br($messageClientTxt) . "</b>";

		//  ********  FICHIER JOINT  ********

		$fichierInitial = $_FILES['pieceJointe'];
		$taille         = $fichierInitial['size']; 	   // en OCTETS
		$nomInitial     = $fichierInitial['name'];     // de la forme "fichier.txt"
		// on extrait l'extension (que l'on force en minuscules) :
		$extension      = strtolower( pathinfo($nomInitial, PATHINFO_EXTENSION) );
		$nomTemporaire  = $fichierInitial['tmp_name']; // de la forme "/tmp/phpxxxxxx", ie que cela inclut le chemin
		$type           = $fichierInitial['type'];
		// ex. image/gif ou image/jpeg ou application/pdf ou text/plain ou application/vnd.ms-excel ou application/octet-stream

		// pour contrer au mieux les failles de sécurité, on procède à quelques tests / modifications
		// sur le fichier joint.

		// 1° => on choisit et on protège l'emplacement de stockage sur le serveur :
		// (penser à bien définir les droits d'accès en lecture / écriture
		//  ainsi qu'un solide .htaccess qui interdira de voir l'index-of du répertoire en question)                                +++++++++++
		$repFinal = "../ordonnances_jointes";

		// 2° => on vérifie que la taille est bien positive mais ne dépasse pas X Mo (cf TAILLE_MAX_PJ) :
		( ! $taille > 0 ) ? $erreurs['pieceJointe'] .= " [vide]" : "";
		( $taille > TAILLE_MAX_PJ ) ? $erreurs['pieceJointe'] .= " [trop volumineux]" : "";

		// 3° => avant de stocker le fichier joint, s'il avait bien un nom, on lui en donne un nouveau,
		//       constitué de la date, du nom du client, suivi de caractères aléatoires :
		if( ! $nomInitial == "" ){
			// avant d'écrire la date dans le nom du fichier, on définit le fuseau horaire par défaut à utiliser :
			( date_default_timezone_set("Europe/Paris") ) ? $fuseau = "" : $fuseau = " (fuseau horaire invalide)";
			$nouveauNom = date("Y-m-d_H-i-s_") . $prenom . "_" . $nom . "_" . bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
		}
		else{
			// le fichier n'avait pas de nom :
			$erreurs['pieceJointe'] .= " [anonyme]";
		}

		// 4° => on vérifie que l'extension du fichier joint fait partie de celles autorisées,
		//       ET on prépare dès maintenant le "Content-type" de la pièce jointe du mail :
		switch ($extension) {
			case 'jpe' :
			case 'jpg' :
			case 'jpeg': $ContentType = "image/jpeg";      break;
			case 'png' : $ContentType = "image/png";       break;
			case 'gif' : $ContentType = "image/gif";       break;
			case 'pdf' : $ContentType = "application/pdf"; break;
			default:
				$erreurs['pieceJointe'] .= " [extension invalide]";
		}

		// 5° => on vérifie qu'un fichier portant le même nom n'est pas déjà présent sur le serveur :
		if( file_exists($repFinal.'/'.$nouveauNom.'.'.$extension)) {
			// ce message là, qui ne devrait pas arriver, écrase les précédents :
			$erreurs['pieceJointe'] = "Erreur serveur, veuillez renvoyer le formulaire svp.";
		}

		// 6° => au final, s'il n'y a pas d'erreurs, ie ni pour la pièce jointe, ni pour les autres champs
		// du formulaire, alors on déplace le fichier :
		if( ! isset($erreurs) ){
			// le fichier est validé, on le déplace à son emplacement définitif tout en le renommant :
			$succes = move_uploaded_file($nomTemporaire , $repFinal.'/'.$nouveauNom.'.'.$extension);
		    if( ! $succes ){
			    // le déplacement n'a pas pu se faire, on supprime le fichier du serveur :
			    unlink($nomInitial);

		    	$erreurs['pieceJointe'] = "[erreur de transfert ". $_FILES['pieceJointe']['error']. "]";
		    	// la valeur de l'erreur renseigne sur sa signification :
		    	// 0 = a priori ce sont les droits d'accès en écriture qui ne sont pas conformes ...
		    	//     ou des caractères non autorisés dans le nom du fichier : ex. "/" ...
		    	// 1 = UPLOAD_ERR_INI_SIZE - Taille du fichier téléchargé > upload_max_filesize dans le php.ini.
				// 2 = UPLOAD_ERR_FORM_SIZE - Taille du fichier téléchargé > MAX_FILE_SIZE définie dans le formulaire HTML.
				// 3 = UPLOAD_ERR_PARTIAL - Le fichier n'a été que partiellement téléchargé.
				// 4 = UPLOAD_ERR_NO_FILE - Aucun fichier n'a été téléchargé.
				// 6 = UPLOAD_ERR_NO_TMP_DIR - Un dossier temporaire est manquant. (introduit en PHP 5.0.3)
				// 7 = UPLOAD_ERR_CANT_WRITE - Échec de l'écriture du fichier sur le disque. (introduit en PHP 5.1.0)
				// 8 = UPLOAD_ERR_EXTENSION - Une extension PHP a arrêté l'envoi de fichier.
				//		PHP ne propose aucun moyen de déterminer quelle extension est en cause.
				//		L'examen du phpinfo() peut aider. (introduit en PHP 5.2.0)
		    }
		}
		else{
			// il y avait des erreurs => on supprime le fichier du serveur :
		    unlink($nomInitial);
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Pharmacie Le Reste</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="shortcut icon" href="img/favicon.ico">
</head>

<body>
	<header>
		<section>
			<a href="index.php">
				<img src="img/croix_mauve.png" alt="">
				<h1>Pharmacie Le Reste</h1>
				<h2>Nantes, quartier Saint-Joseph de Porterie</h2>
			</a>
			<p id="telIndex"><i class="fa fa-volume-control-phone" aria-hidden="true"></i>&nbsp;&nbsp;<a href="tel:+33240251580">02 40 25 15 80</a></p>
		</section>
		<nav class="navigation">
			<ul>
				<li><a href="index.php"   >Accueil </a></li>
				<li><a href="horaires.php">Horaires</a></li>
				<li><a href="equipe.html"  >Équipe  </a></li>
				<li><a href="contact.php"  >Contact </a></li>
			</ul>
		</nav>
	</header>

	<main>
		<section class="formOrdo"><h3>Préparation d'ordonnance</h3>
 
			<?php if( isset($_POST['bouton']) && !isset($erreurs)) : ?>

				<?php

				//    le formulaire a été rempli  ET  s'il n'y a pas d'erreurs
				//
				//    => on envoie le mail ! (après avoir préparé les données)

				/////////////////////////////////////////////////////
				//
				// préparation des infos ajoutées dans le mail
				//
				/////////////////////////////////////////////////////

					// ===================  date  =================== //

					$res = setlocale(LC_TIME, "fra");
					($res === false) ? "false" : $res;
					// $date = "Semaine " . date('W - D d/m/Y - H:i:s') . $fuseau; // $fuseau a été défini plus haut, en cas d'erreur (sinon il est vide)
					$date = "Semaine " . strftime('%W - %A %d/%B/%Y - %H:%M:%S') . $fuseau; // $fuseau a été défini plus haut, en cas d'erreur (sinon il est vide)

					// ===============  IP du client  =============== //     (3 possibilités)

					$ipClient = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
					if( empty($ipClient) ){
						$ipClient = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? "HTTP_X " . $_SERVER['HTTP_X_FORWARDED_FOR'] : "";
						if( empty($ipClient) ){
							$ipClient = isset($_SERVER['HTTP_CLIENT_IP']) ? "HTTP_C " . $_SERVER['HTTP_CLIENT_IP'] : "";	
						}
						else{
							// si jamais HTTP_X... était remplie, on la compare avec HTTP_C... et
							// si jamais les 2 sont différentes, on garde les 2 infos :
							if( isset($_SERVER['HTTP_CLIENT_IP']) ){
								if( strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['HTTP_CLIENT_IP']) != 0 ){
									$ipClient .= " ■ HTTP_C " . $_SERVER['HTTP_CLIENT_IP'];
								}
							}
						}
					}
					else{
						// si jamais REMOTE était remplie, on la compare avec HTTP_X... et
						// si jamais les 2 sont différentes, on garde les 2 infos :
						if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ){
							if( strcasecmp($ipClient, $_SERVER['HTTP_X_FORWARDED_FOR']) != 0 ){
								$ipClient .= " ■ HTTP_X " . $_SERVER['HTTP_X_FORWARDED_FOR'];
							}
						}
					}

					// ===================  FAI  ==================== //

					$faiClientBrut = gethostbyaddr(getIpAdr());
					// a priori, quand ça vient du réseau mobile, ce n'est pas le FAI mais l'IP,
					// d'où le message plus explicite :
					if( strcasecmp($faiClientBrut, $ipClient) == 0 ){
						$faiClientBrut = "inconnu (message issu du réseau mobile)";
					}
					// else{
					// $faiClientBrut contient, en plus du FAI, plusieurs autres infos, d'où :
					// $faiClient = substr(stristr($faiClientBrut, "ipv"), 5);
					// }


				///////////////////////////////////////////////////////////
				//
				// ENFIN, construction du mail
				//
				// (en suivant qqs étapes préparatoires encore une fois)
				//
				///////////////////////////////////////////////////////////

					// apparemment, à une époque, le CRLF était différent selon les clients mails (\r\n, ou \n, voire \r)
					// mais après plusieurs tests au cours desquels j'ai essayé les 2 1ères versions,
					// il semble qu'il n'y ait plus de problème, les 2 fonctionnent bien ... cf fichier texte "CRLF.txt"
					$rc = "\r\n";

					// NB: Pour ce qui est du charset, le ISO-8859-1 est supporté par tous les webmails,
					//     contrairement à l'UTF-8, mais ne permet pas les accents français,
					//     alors que l'UTF-8 les supportent ... donc je choisis UTF-8


					// ===============  Objet du mail  ============== //

					// L'objet du message est constitué d'un préfixe (les 4 derniers car. de l'IP) suivi des prénom et nom de l'expéditeur :
					// (la fonction mb... sert à autoriser les caractères accentués)
					$objet = mb_encode_mimeheader("Ordonnance - [" .
													substr($ipClient, -4, 4) . "]  " .
													$civilite . " " .
													$prenom . " " .
													$nom, "UTF-8", "B");

					// ==========  Création du séparateur  ========== //

					// ici il nous en faut 2 puisque nous devons séparer les 2 alternatives text : plain et html,
					// ainsi que le message proprement dit et la pièce jointe
					$boundary_A = md5(rand());
					$boundary_B = md5(rand());
					$separateur_A = $rc . "--" . $boundary_A . $rc; // on intègre les 2 "--" obligatoires et les CRLF
					$separateur_B = $rc . "--" . $boundary_B . $rc;

					// ============  Création du header  ============ //

					// cf dossier "envoi de mails en PHP"
					$header =	"From: " .
								mb_encode_mimeheader(LABEL_EXP, "UTF-8", "B") .
								"<" . ADR_EXP_HEBERGEUR . ">" . $rc .
								"Reply-To: $adrMailClient" . $rc .
								"MIME-Version: 1.0" . $rc .
								"X-Mailer: PHP/" . phpversion() . $rc .
								"Content-Type: multipart/mixed; boundary=" . $boundary_A;

					// ============= Création du message ============= //

					// Texte placé entre le header et le message proprement dit,
					// pour les clients mails ne supportant pas le type MIME (ça ne doit pas être très fréquent !..)
	           		$message = $rc . "Type MIME non pris en charge par votre client mail ..." . $rc;

					// on introduit les 2 versions alternatives :
					$message .=	$separateur_A . 
								"Content-Type: multipart/alternative; boundary=" . $boundary_B;

					// version "TEXT"
					$message .=	$separateur_B .
								"Content-Type: text/plain; charset=\"UTF-8\"" . $rc .
								"Content-Transfer-Encoding: 8bit" . $rc .
								$date . " - " . $civilite . " " . $prenom . " " . $nom . "  -  " . $adrMailClient . $rc . $rc .
								$messageClientTxt . $rc . $rc. $rc . $rc .
								"IP  client     = " . $ipClient . $rc .
								"FAI client     = " . $faiClientBrut;

					// version "HTML"
					$message .=	$separateur_B .
								"Content-Type: text/html; charset=\"UTF-8\"" . $rc .
								"Content-Transfer-Encoding: 8bit" . $rc .
								$date . " - <b>" . $civilite . " " . $prenom . " " . $nom . "</b>  -  " . $adrMailClient . "<br><br>" .
								$messageClientHtml . "<br><br><br><br>" .
								"IP  client     = " . $ipClient . "<br>" .
								"FAI client     = " . $faiClientBrut;

					// ======== Insertion de la pièce jointe ========= //

					// 1- on ouvre le fichier en lecture seule :
					$flux = fopen($repFinal.'/'.$nouveauNom.'.'.$extension, 'r') or die("impossible à ouvrir !");

					// 2- on parcourt l'ensemble du fichier :
					$pieceJointe = fread( $flux, filesize($repFinal.'/'.$nouveauNom.'.'.$extension) );

					// 3- on referme le fichier :
					fclose($flux);

					// 4- on encode la pièce jointe :
					$pieceJointe = chunk_split(base64_encode($pieceJointe));

					// 5- on ajoute la PJ dans le mail :
					//    (le Content-type a été préparé au moment des vérifications sur la pièce jointe)
					$message .= $separateur_A .
								"Content-Type: " . $ContentType . "; name=" . $nouveauNom.'.'.$extension . $rc . // ex. "image/jpeg"
								"Content-Transfer-Encoding: base64" . $rc .
								"Content-Disposition: attachment; filename=" . $nouveauNom.'.'.$extension . $rc .
								$pieceJointe;

					// ============= Dernier "blindage" ============== //

					// si le formulaire n'est pas posté de notre site, on renvoie vers la page d'accueil
					if(    strcmp( $_SERVER['HTTP_REFERER'], ADRESSE_SITE_PHARMACIE . PAGE_EN_COURS ) != 0
						&& strcmp( $_SERVER['HTTP_REFERER'], S_ADRESSE_SITE_PHARMACIE . PAGE_EN_COURS ) != 0
						&& strcmp( $_SERVER['HTTP_REFERER'], W_ADRESSE_SITE_PHARMACIE . PAGE_EN_COURS ) != 0
						&& strcmp( $_SERVER['HTTP_REFERER'], SW_ADRESSE_SITE_PHARMACIE . PAGE_EN_COURS ) != 0 ){

						$headerAlerte =	"From: " .
										mb_encode_mimeheader("Expéditeur indésirable", "UTF-8", "B") .
										"<" . ADR_EXP_HEBERGEUR . ">" . $rc .
										"Reply-To: " . $rc .
										"MIME-Version: 1.0" . $rc .
										"X-Mailer: PHP/" . phpversion() . $rc .
										"Content-Type: text/plain; charset=\"UTF-8\"" . $rc .
										"Content-Transfer-Encoding: 8bit";
						$messageAlerte =	$date . " - " . $prenom . " " . $nom . "  -  " . $adrMailClient . $rc . $rc .
											"Envoi du formulaire à partir d'un site web différent de celui de la pharmacie :" . $rc .
											$_SERVER['HTTP_REFERER'] . $rc . $rc .
											"IP  client     = " . $ipClient . $rc .
											"FAI client     = " . $faiClientBrut;
						mail(MAIL_DEST_PHARMA, "Tentative de piratage ?", $messageAlerte, $headerAlerte);
					    header('Location: https://www.bigouig.fr/'); 
					} 
					else{
					    // envoi de l'e-mail :
						if( mail(MAIL_DEST_PHARMA, $objet, $message, $header) ){

							echo "<div class='artMessageConfirm'>";
							echo "<style type='text/css'> h3 { display: none } </style>"; // pour effacer le titre de la page : "Préparation ..."
							echo "<p>Merci, votre ordonnance a bien été envoyée.</p>";
							echo "<p>Nous vous répondrons dans les meilleurs délais, sous réserve qu'il n'y ait pas d'erreur dans l'adresse mail fournie.</p>";
							echo "</div>";
						}
						else{
							echo "<div class='artMessageConfirm'>";
							echo "<style type='text/css'> h3 { display: none } </style>"; // pour effacer le titre de la page : "Préparation ..."
							echo "<p>Aïe, il y a eu un problème ...</p>";
							echo "<p>Le serveur est probablement indisponible, veuillez réessayer ultérieurement, merci.</p>";
							echo "</div>";
						}
					}; 
					?>

			<?php else : ?>

				<?php

				// - soit il y a eu des erreurs dans le formulaire
				//   => alors on ré-affiche les valeurs saisies (grâce à "value"),
				//      ainsi qu'un message d'erreur pour les valeurs concernées.
				//
				// - soit le formulaire n'a pas encore été rempli
				//   => on laisse les cases vides.
				?>
				<div class="artIntro">
					<p>Envoyez-nous votre ordonnance via le formulaire ci-dessous.</p>
					<p>Les produits seront alors aussitôt préparés et vous serez prévenu(e) par mail de leur mise à disposition.</p>
					<p><span class="attention">Attention</span>, venez à la pharmacie <span class="attention">avec l'original de l'ordonnance</span>.</p>
					<p>Pensez aussi à la <span class="attention">carte vitale</span> et à la <span class="attention">carte de mutuelle</span>.</p>
					<p>Si tous les produits sont en stock, le délai moyen de préparation est d'environ 2 h, sinon une demi-journée suffit en général.</p>
				</div>

				<article><a href="#" id="idModeEmploi"><h4>Mode d'emploi</h4></a>

					<div class="divModeEmploi">
						<div>Il suffit de suivre ces <span>4 étapes :</span></div>
						<ol>
							<li><span>numériser l'ordonnance :</span>
								<ul>
									<li>si vous sortez de chez le médecin avec votre document papier,
									    vous pouvez simplement le photographier avec votre smartphone.
									    Attention, prenez garde à bien cadrer l'ordonnance qui doit être visible en totalité.</li>
									<li>de chez vous, si vous disposez d'une imprimante-scanner, vous pouvez numériser le document.</li>
									<li>si l'ordonnance est déjà sous forme de document PDF, il n'y a rien de plus à faire.</li>
								</ul>
							</li>

							<li><span>remplir tous les champs du formulaire.</span></li>

							<li><span>joindre le document numérisé de votre ordonnance :</span>
								<ul>
									<li><p>cliquer sur "Parcourir ..."</p>
										<p>NB: il est possible que certains smartphones ne permettent pas l'accès aux fichiers du téléphone</p></li>
									<li>sélectionner le document créé à l'étape 1)</li>
								</ul>
							</li>

							<li><span>cliquer sur "Envoyer".</span></li>
						</ol>
						Nous nous occupons de la suite !
					</div>
				</article>
				<span class="obligatoire">(la saisie de tous les champs est obligatoire ; pièce jointe < 2 Mo)</span>
				<form method="POST" enctype="multipart/form-data">
					<div class="champsForm">
						<input type="radio" id="idCiviliteMme" name="civilite" value="Mme" required
							<?= isset($civilite) && $civilite == "Mme" ? "checked" : ""?> >
						<label for="idCiviliteMme">Mme</label>
						<input type="radio" id="idCiviliteMlle" name="civilite" value="Mlle" required
							<?= isset($civilite) && $civilite == "Mlle" ? "checked" : ""?> >
						<label for="idCiviliteMlle">Melle</label>
						<input type="radio" id="idCiviliteM" name="civilite" value="M." required
							<?= isset($civilite) && $civilite == "M." ? "checked" : ""?> >
						<label for="idCiviliteM">M.</label>
					</div>
					<div class="champsForm">
						<label for="idPrenom">Prénom</label>
								<input type="text" id="idPrenom" name="prenom" minlength="<?= NB_CAR_MIN_HTM ?>" maxlength="<?= NB_CAR_MAX_HTM ?>" required <?= isset($prenom) ? "value=" . $prenom : ""?> >
					<?php if( isset($erreurs['prenom']) ) { echo "<p><span>" . $erreurs['prenom'] . "</span></p>"; } ?>
					</div>

					<div class="champsForm">
						<label for="idNom">Nom</label>
								<input type="text" id="idNom" name="nom" minlength="<?= NB_CAR_MIN_HTM ?>" maxlength="<?= NB_CAR_MAX_HTM ?>" required <?= isset($nom) ? "value=" . $nom : ""?> >
					<?php if( isset($erreurs['nom']) ) { echo "<p><span>" . $erreurs['nom'] . "</span></p>"; } ?>
					</div>

					<div class="champsForm">
						<label for="idMail">Mail</label>
								<input type="email" id="idMail" name="adrMailClient" required <?= isset($adrMailClient) ? "value=" . $adrMailClient : ""?> >
					<?php if( isset($erreurs['adrMailClient']) ) { echo "<p><span>" . $erreurs['adrMailClient'] . "</span></p>"; } ?>
					</div>
					<div class="champsForm">
						<label for="idPJ">Ordonnance</label>
								<input type="file"	id="idPJ" name="pieceJointe" accept=<?= LISTE_EXT_AUTORISEES ?> required >
					<?php if( isset($erreurs['pieceJointe']) ) { echo "<p><span>" . $erreurs['pieceJointe'] . "</span></p>"; } ?>
					</div>
					<div class="champsForm">
							<p>Apportez-nous des précisions qui vous semblent utiles sur votre traitement.
								<br>Peut-être avez-vous déjà certains produits qu'il serait donc inutile d'ajouter à la commande ?..</p>
						<label for="idMessage">Message</label>
								<textarea rows="7" minlength="<?= NB_CAR_MIN_MESSAGE_HTM ?>" maxlength="<?= NB_CAR_MAX_MESSAGE_HTM ?>" id="idMessage" name="message" required><?= isset($messageClientTxt) ? $messageClientTxt : "" ?></textarea>
					<?php if( isset($erreurs['message']) ) { echo "<p><span>" . $erreurs['message'] . "</span></p>"; } ?>
					</div>

					<div class="envoyer">
						<button name="bouton">Envoyer</button>
					</div>
				</form>
			<?php endif ?>
		</section>
	</main>

	<footer>
		<section><h3>Coordonnées de la pharmacie Le Reste</h3>
			<p>Pharmacie Le Reste</p>
			<p>21 rue du Bêle</p>
			<p>44300 Nantes</p>
			<p>tel - 02 40 25 15 80</p>
			<p>fax - 02 40 30 06 56</p>
		</section>
		<section><h3>Informations sur l'editeur du site</h3>
			<p>Édition CLR - 2017</p>
		</section>
	</footer>
</body>
</html>