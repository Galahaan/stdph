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
	if( strpos($page, 'admin') ){
	// if( preg_match('admin', $page) ){                        ok en PHP 5.6.30 mais plus en PHP 7.1.4  ********************
		echo "Vous n'avez pas accès à ce répertoire";
	}
	else{
	    // On vérifie que la page est bien sur le serveur
	    if (file_exists("includes/" . $page) && $page != 'index.php') {
	    	include("./includes/".$page);
	    }
	    else{
	    	echo "Erreur Include : le fichier " . $page . " est introuvable.";
	    }
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	/////     FIN INCLUDE sécurisé
	///////////////////////////////////////////////////////////////////////////////////////////////

	// Définition de quelques CONSTANTES :

	// taille max de la pièce jointe : 2 Mo = 2097152 octets
	define("TAILLE_MAX_PJ", 2097152);

	// tableau listant les extensions autorisées pour la pièce jointe :
	$extensionsOK = array("jpg", "jpeg", "png", "gif", "pdf");

	// adresse mail destinataire :
	define("MAIL_DEST_PHARMA", "phcie.lereste@perso.alliadis.net");
	define("MAIL_DEST_TEST", "9byjpuk5k@use.startmail.com");

	// Nombre de caractères min et max pour les nom et prénom :
	define("NB_CAR_MIN", 2);
	define("NB_CAR_MAX", 40);

	// si on veut utiliser la vérification naturelle du HTML :
	define("NB_CAR_MIN_HTM", 2);
	define("NB_CAR_MAX_HTM", 40);

	// Nombre de caractères min et max pour le texte libre :
	define("NB_CAR_MIN_MESSAGE", 40);
	define("NB_CAR_MAX_MESSAGE", 1000);

	// si on veut utiliser la vérification naturelle du HTML :
	define("NB_CAR_MIN_MESSAGE_HTM", 0);
	define("NB_CAR_MAX_MESSAGE_HTM", 1000);

	// Si le formulaire vient d'être validé, et avant de savoir si on va envoyer le mail, on "nettoie" les champs :
	if( isset($_POST['bouton']) ){

		//  ********  PRENOM  ********

		$prenom = htmlspecialchars(strip_tags($_POST['prenom']));
		if( (strlen($prenom) < NB_CAR_MIN) || (strlen($prenom) > NB_CAR_MAX ) ){
			$erreurs['prenom'] = "(entre " . NB_CAR_MIN . " et " . NB_CAR_MAX . " caractères)";
		}

		//  ********  NOM  ********

		$nom = htmlspecialchars(strip_tags($_POST['nom']));
		if( (strlen($nom) < NB_CAR_MIN) || (strlen($nom) > NB_CAR_MAX ) ){
			$erreurs['nom'] = "(entre " . NB_CAR_MIN . " et " . NB_CAR_MAX . " caractères)";
		}

		// on les écrit sous le format : Prénom NOM
		$prenom = ucfirst(strtolower($prenom));
		$nom = strtoupper($nom);

		//  ********  MAIL  ********

		// "nettoie" la valeur utilisateur :
		$adrMailExp = filter_var($_POST['adrMailExp'], FILTER_SANITIZE_EMAIL);

		// teste la NON validité du format :
		if( ! filter_var($adrMailExp, FILTER_VALIDATE_EMAIL) ){
			$erreurs['adrMailExp'] = "(format incorrect)"; 
		};

		//  ********  MESSAGE  ********

		// on traite volontairement le cas du message AVANT celui de la pièce jointe pour
		// ne conserver la pièce jointe QUE si AUCUNE erreur n'a été détectée dans les test.
		$message = htmlspecialchars(strip_tags($_POST['message']));
		if( (strlen($message) < NB_CAR_MIN_MESSAGE) || (strlen($message) > NB_CAR_MAX_MESSAGE ) ){
			$erreurs['message'] = "(entre " . NB_CAR_MIN_MESSAGE . " et " . NB_CAR_MAX_MESSAGE . " caractères)";
		}

		//  ********  FICHIER  ********

		$fichierInitial = $_FILES['pieceJointe'];
		$taille         = $fichierInitial['size']; 	   // en OCTETS
		$nomInitial     = $fichierInitial['name'];     // de la forme "fichier.txt"
		// on extrait l'extension (que l'on force en minuscules) :
		$extension      = strtolower( pathinfo($nomInitial, PATHINFO_EXTENSION) );
		$repTempInitial = $fichierInitial['tmp_name']; // de la forme "/tmp/phpxxxxxx"
		$type           = $fichierInitial['type'];
		// ex. image/gif ou image/jpeg ou application/pdf ou text/plain ou application/vnd.ms-excel ou application/octet-stream

		// pour contrer au mieux les failles de sécurité, on procède à quelques tests / modifications
		// sur le fichier joint.

		// 1° => on choisit et on protège l'emplacement de stockage sur le serveur :
		// (penser à bien définir les droits d'accès en lecture / écriture
		//  ainsi qu'un solide .htaccess qui interdira de voir l'index-of du répertoire en question) +++++++++++++++++++++++++++++
		$repFinal = "../ordonnances_jointes";

		// 2° => on vérifie que la taille est bien positive mais ne dépasse pas X Mo (cf TAILLE_MAX_PJ) :
		( ! $taille > 0 ) ? $erreurs['pieceJointe'] .= " [vide]" : "";
		( $taille > TAILLE_MAX_PJ ) ? $erreurs['pieceJointe'] .= " [trop volumineux]" : "";

		// 3° => avant de stocker le fichier joint, s'il avait bien un nom, on lui en donne un nouveau,
		//       constitué de la date, du nom du client, suivi de caractères aléatoires :
		if( ! $nomInitial == "" ){
			( date_default_timezone_set("Europe/Paris") ) ? $fuseau = "" : $fuseau = " (fuseau horaire invalide)";
			$nouveauNom = date("Y-m-d_H:i:s_") . $nom . "_" . $prenom . "_" . bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
		}
		else{
			// le fichier n'avait pas de nom :
			$erreurs['pieceJointe'] .= " [anonyme]";
		}

		// 4° => on vérifie que l'extension du fichier joint fait partie de celles autorisées :
		//       ($extensionsOK définie dans les constantes en haut de ce fichier)
		if( ! in_array($extension, $extensionsOK) ){
			$erreurs['pieceJointe'] .= " [extension invalide]";
		}

		// 5° => on vérifie qu'un fichier portant le même nom n'est pas déjà présent sur le serveur :
		if( file_exists($repFinal.'/'.$nouveauNom.'.'.$extension)) {
			// ce message là, qui ne devrait pas arriver, écrase les précédents :
			$erreurs['pieceJointe'] = "Erreur serveur, veuillez renvoyer le formulaire svp.";
		}

		// Au final, s'il n'y a pas d'erreurs, ie ni pour la pièce jointe, ni pour les autres champs
		// du formulaire, alors on déplace le fichier :
		if( ! isset($erreurs) ){
			// le fichier est validé, on le déplace à son emplacement définitif tout en le renommant :
			$succes = move_uploaded_file($repTempInitial, $repFinal.'/'.$nouveauNom.'.'.$extension);
		    if( ! $succes ){
			    // le déplacement n'a pas pu se faire, on supprime le fichier du serveur :
			    unlink($nomInitial);

		    	$erreurs['pieceJointe'] = "[erreur de transfert ". $_FILES['pieceJointe']['error']. "]";
		    	// la valeur de l'erreur renseigne sur sa signification :
		    	// 0 = a priori ce sont les droits d'accès en écriture qui ne sont pas conformes ...
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
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="shortcut icon" href="img/favicon.ico">
</head>

<body>
	<header>
		<section>
			<a href="index.html">
				<img src="img/croix_mauve.png" alt="">
				<h1>Pharmacie Le Reste
					<p>Nantes, quartier Saint-Joseph de Porterie</p>
				</h1>
			</a>
			<p id="telIndex"><a href="tel:+33240251580">02 40 25 15 80</a></p>
		</section>
		<nav class="navigation">
			<ul>
				<li><a href="index.html"   >Accueil </a></li>
				<li><a href="horaires.html">Horaires</a></li>
				<li><a href="equipe.html"  >Équipe  </a></li>
				<li><a href="contact.php"  >Contact </a></li>
			</ul>
		</nav>
	</header>

	<main>
		<section class="formsContOrdo">
 
			<?php if( isset($_POST['bouton']) && !isset($erreurs)) : ?>

				<?php

				//    le formulaire a été rempli  ET  s'il n'y a pas d'erreurs
				//
				// => on envoie le mail ! (après avoir préparé les données)

				// ajout de quelques infos dans le message :

				// date :
				$date = date('d/m/Y - H:h:i') . $fuseau; // $fuseau a été défini plus haut, en cas d'erreur (sinon il est vide)

				// IP du client : (3 possibilités)
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

				// FAI :
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

				// URL provenance :
				$urlProvenance = $_SERVER['HTTP_REFERER'];

				// enfin, construction du message complet :
				$message =	$date . " - " . $prenom . " " . $nom . "  -  " . $adrMailExp . "<br><br>" .
							nl2br($message) . "<br><br><br>" .
							"URL provenance = " . $urlProvenance . "<br>" .
							"IP  client     = " . $ipClient . "<br>" .
							"FAI client     = " . $faiClientBrut . "<br>";
							// "REMOTE " . $_SERVER['REMOTE_ADDR'] . "<br>" . 
							// "HTTP_X " . $_SERVER['REMOTE_ADDR'] . "<br>" . 
							// "HTTP_C " . $_SERVER['REMOTE_ADDR'];

				// adjonction de la pièce jointe :
				// 1- on ouvre le fichier en lecture seule :
				$fichier = fopen($repFinal.'/'.$nouveauNom.'.'.$extension, 'r');
				// 2- on parcourt l'ensemble du fichier :
				$pieceJointe = fread( $fichier, filesize($repFinal.'/'.$nouveauNom.'.'.$extension) );
				// 3- on referme le fichier :
				fclose($fichier);
				// 4- on encode la pièce jointe :
				$pieceJointe = chunk_split(base64_encode($pieceJointe));



				// ajout d'options pour le message :
				$headers = "From: contact@pharmacielereste.fr\r\n" .
						   "Reply-To: $adrMailExp\r\n" .
				           "MIME-Version: 1.0" . "\r\n" .
				           "Content-type: multipart/mixed; charset=UTF-8" . "\r\n" .
				           "Content-Transfer-Encoding: 8bit" . "\r\n" .
				           "X-Mailer: PHP/" . phpversion();

				// L'objet du message sera constitué d'un préfixe (les 4 derniers car. de l'IP)
				// suivi des prénom et nom de l'expéditeur :
				$objet = "Ordonnance - [" . substr($ipClient, -4, 4) . "]  " . $prenom . " " . $nom;

				if( mail(MAIL_DEST_TEST, $objet, $message, $headers) ){

					echo "<p>Merci, votre ordonnance a bien été envoyée.</p>";
					echo "<p>Nous vous répondrons dans les meilleurs délais, sous
							réserve qu'il n'y ait pas d'erreur dans l'adresse mail fournie.</p>";
				}
				else{
					echo "<p>Aïe, il y a eu un problème ...</p>";
					echo "<p>Le serveur est probablement indisponible, veuillez réessayer ultérieurement, merci.</p>";
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
				<article class="ordoIntro">
					<p>Envoyez-nous votre ordonnance via le formulaire ci-dessous.</p>
					<p>Les produits seront alors aussitôt préparés et vous serez prévenu(e) par mail / sms de leur mise à disposition.</p>
					<p>Si tous les produits sont en stock, le délai moyen de préparation est d'environ 2h, sinon une demi-journée suffit en général.</p>

					<p class="espaceVertical"></p>
					
					<ol>Il suffit de suivre ces 4 étapes :
						<li>numériser l'ordonnace :
							<ul>
								<li>- si vous sortez de chez le médecin avec votre document papier,
								      vous pouvez simplement le photographier avec votre smartphone.
								      Attention, prenez garde à bien cadrer l'ordonnance qui doit être visible en totalité.</li>
								<li>- de chez vous, si vous disposez d'une imprimante-scanner, vous pouvez numériser le document.</li>
								<li>- si l'ordonnance est déjà sous forme de document PDF, il n'y a rien de plus à faire.</li>
							</ul>
						</li>

						<p class="espaceVertical"></p>

						<li>remplir tous les champs obligatoires du formulaire.</li>

						<p class="espaceVertical"></p>

						<li>joindre le document numérisé de votre ordonnance :
							<ul>
								<li>cliquer sur "Parcourir ..."</li>
								<li>sélectionner le document créé à l'étape 1)</li>
							</ul>
						</li>

						<p class="espaceVertical"></p>

						<li>cliquer sur "Envoyer".</li>
					</ol>

					<p class="espaceVertical"></p>

					<p> Nous nous occupons de la suite !</p>

					<p class="espaceVertical"></p>

				</article>

				<form method="POST" enctype="multipart/form-data">
					<div class="champsForm">
						<label for="idPrenom">Prénom <span>*</span></label>
								<input type="text" id="idPrenom" name="prenom" minlength="<?= NB_CAR_MIN_HTM ?>" maxlength="<?= NB_CAR_MAX_HTM ?>" required <?= isset($prenom) ? "value=" . $prenom : ""?> >
					<?php if( isset($erreurs['prenom']) ) { echo "<span>" . $erreurs['prenom'] . "</span>"; } ?>
					</div>

					<div class="champsForm">
						<label for="idNom">Nom <span>*</span></label>
								<input type="text" id="idNom" name="nom" minlength="<?= NB_CAR_MIN_HTM ?>" maxlength="<?= NB_CAR_MAX_HTM ?>" required <?= isset($nom) ? "value=" . $nom : ""?> >
					<?php if( isset($erreurs['nom']) ) { echo "<span>" . $erreurs['nom'] . "</span>"; } ?>
					</div>

					<div class="champsForm">
						<label for="idMail">Mail <span>*</span></label>
								<input type="email" id="idMail" name="adrMailExp" required <?= isset($adrMailExp) ? "value=" . $adrMailExp : ""?> >
					<?php if( isset($erreurs['adrMailExp']) ) { echo "<span>" . $erreurs['adrMailExp'] . "</span>"; } ?>
					</div>
					<div class="champsForm">
						<label for="idPJ">Ordonnance <span>*</span></label>
								<input type="file" id="idPJ" name="pieceJointe" required>
					<?php if( isset($erreurs['pieceJointe']) ) { echo "<span>" . $erreurs['pieceJointe'] . "</span>"; } ?>
					</div>
					<div class="champsForm">
						<label for="idMessage">Message <span>*</span></label>
								<textarea rows="4" minlength="<?= NB_CAR_MIN_MESSAGE_HTM ?>" maxlength="<?= NB_CAR_MAX_MESSAGE_HTM ?>" id="idMessage" name="message" required><?= isset($message) ? $message : ""?></textarea>
					<?php if( isset($erreurs['message']) ) { echo "<span>" . $erreurs['message'] . "</span>"; } ?>
					</div>

					<p><span>* saisie obligatoire</span></p>
					<div class="envoyer">
						<div class="caseDeGauche"></div>
						<div class="caseDeDroite">
							<button name="bouton">Envoyer</button>
						</div>
					</div>
				</form>
			<?php endif ?>
		</section>
	</main>

	<footer>
		<section>
			<p>Pharmacie Le Reste</p>
			<p>21 rue du Bêle</p>
			<p>44300 Nantes</p>
			<p>tel - 02 40 25 15 80</p>
			<p>fax - 02 40 30 06 56</p>
		</section>
		<section>
			<p>Édition CLR - 2017</p>
		</section>
	</footer>
</body>
</html>

<script language="JavaScript">
    <!-- hide the script from old browsers --
    //written by W.Moshammer
    function yhostip(){
      if((navigator.appName == "Microsoft Internet Explorer") && 
        ((navigator.appVersion.indexOf('3.') != -1) || 
        (navigator.appVersion.indexOf('4.') != -1)))
        document.write("Not with MS IE 3.0/4.0");
      else {
        window.onerror=null;	
        yourAddress =java.net.InetAddress.getLocalHost();	
        yourAddress2=java.net.InetAddress.getLocalHost();	
        yhost       =yourAddress.getHostName();	
        yip         =yourAddress2.getHostAddress();
        document.write("Your host name is "+yhost);
        document.write("<br>Your IP address is "+yip);  
      }
    }
   //--end hiding here -->
  </script>