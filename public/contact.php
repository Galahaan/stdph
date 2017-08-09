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

	// adresse mail destinataire :
	define("MAIL_DEST_PHARMA", "phcie.lereste@perso.alliadis.net");
	define("MAIL_DEST_TEST", "9byjpuk5k@use.startmail.com");

	// Nombre de caractères min et max pour les nom et prénom :
	define("NB_CAR_MIN", 2);
	define("NB_CAR_MAX", 40);

	// si on veut utiliser la vérification naturelle du HTML :
	define("NB_CAR_MIN_HTM", 1);
	define("NB_CAR_MAX_HTM", 40);

	// Nombre de caractères min et max pour le texte libre :
	define("NB_CAR_MIN_MESSAGE", 20);
	define("NB_CAR_MAX_MESSAGE", 1000);

	// si on veut utiliser la vérification naturelle du HTML :
	define("NB_CAR_MIN_MESSAGE_HTM", 2);
	define("NB_CAR_MAX_MESSAGE_HTM", 1000);

	// Si le formulaire vient d'être validé, on "nettoie" les champs :
	if( isset($_POST['bouton']) ){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";

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

		$message = htmlspecialchars(strip_tags($_POST['message']));
		if( (strlen($message) < NB_CAR_MIN_MESSAGE) || (strlen($message) > NB_CAR_MAX_MESSAGE ) ){
			$erreurs['message'] = "(entre " . NB_CAR_MIN_MESSAGE . " et " . NB_CAR_MAX_MESSAGE . " caractères)";
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
			<img src="img/croix_mauve.png">
				<h1>Pharmacie Le Reste
					<p>Nantes, quartier Saint-Joseph de Porterie</p>
					<p>02 40 25 15 80</p>
				</h1>
			</a>
		</section>
		<nav>
			<ul>
				<li><a href="index.html"   >Accueil </a></li>
				<li><a href="horaires.html">Horaires</a></li>
				<li><a href="equipe.html"  >Équipe  </a></li>
				<li><a href="contact.php"  >Contact </a></li>
			</ul>
		</nav>

	</header>

	<main>

		<section class="formsContOrdo gauche">
			<p>Pharmacie Le Reste</p>
			<p>21 rue du Bêle</p>
			<p>44300 Nantes</p>
			<p><i class="fa fa-phone"    aria-hidden="true"></i>02 40 25 15 80</p>
			<p><i class="fa fa-fax"      aria-hidden="true"></i>02 40 30 06 56</p>
			<p><a href="mailto:contact@pharmacielereste.fr"><i class="fa fa-envelope" aria-hidden="true"></i>contact@pharmacielereste.fr</a></p>
			<p>
				<a href="https://www.facebook.com/Pharmacie-Le-Reste-700447003388902">
					<img class="fbg couleur" src="img/fb.png">
					<img class="fbg noir" src="img/fb_n.png">
				</a>
			</p>
			<p>
				<a href="https://plus.google.com/113407799173132476603/about">
					<img class="fbg couleur" src="img/gplus.png">
					<img class="fbg noir" src="img/gplus_n.png">
				</a>
			</p>
		</section>

		<section class="formsContOrdo droite">
 
			<?php if( isset($_POST['bouton']) && !isset($erreurs)) : ?>

				<?php

				//    si le formulaire est rempli  ET  s'il n'y a pas d'erreurs
				//
				// => on envoie le mail ! (après avoir préparé les données)

				// ajout de quelques infos dans le message :

				// date :
				$date = date('d')."/".date('m')."/".date('Y')." - ".date('H')."h".date('i');

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

				// ajout d'options pour le message :
				$headers = "From: contact@pharmacielereste.fr\r\n" .
						   "Reply-To: $adrMailExp\r\n" .
				           "MIME-Version: 1.0" . "\r\n" .
				           "Content-type: text/html; charset=UTF-8" . "\r\n" .
				           "Content-Transfer-Encoding: 8bit" . "\r\n" .
				           "X-Mailer: PHP/" . phpversion();

				// L'objet du message sera constitué d'un préfixe (les 4 derniers car. de l'IP)
				// suivi des prénom et nom de l'expéditeur :
				$objet = "Contact - [" . substr($ipClient, -4, 4) . "]  " . $prenom . " " . $nom;

				if( mail(MAIL_DEST_TEST, $objet, $message, $headers) ){

					echo "<h2>Merci, votre message a bien été envoyé.</h2>";
					echo "<p>Nous vous répondrons dans les meilleurs délais, sous
							réserve qu'il n'y ait pas d'erreur dans l'adresse mail fournie.</p>";
				}
				else{
					echo "<h2>Aïe, il y a eu un problème ...</h2>";
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
				<h2> Envoyez-nous un message ...</h2>
				<section>
					<form method="post">
						<div class="champsForm">
							<label for="idPrenom">Prénom <span>*</span></label>
									<input type="text" id="idPrenom" name="prenom" minlength="<?= NB_CAR_MIN_HTM ?>" maxlength="<?= NB_CAR_MAX_HTM ?>" required <?= isset($prenom) ? "value=" . $prenom : ""?> >
						<?php if( isset($erreurs['prenom']) ) { echo "<aside>" . $erreurs['prenom'] . "</aside>"; } ?>
						</div>

						<div class="champsForm">
							<label for="idNom">Nom <span>*</span></label>
									<input type="text" id="idNom" name="nom" minlength="<?= NB_CAR_MIN_HTM ?>" maxlength="<?= NB_CAR_MAX_HTM ?>" required <?= isset($nom) ? "value=" . $nom : ""?> >
						<?php if( isset($erreurs['nom']) ) { echo "<aside>" . $erreurs['nom'] . "</aside>"; } ?>
						</div>

						<div class="champsForm">
							<label for="idMail">Mail <span>*</span></label>
									<input type="email" id="idMail" name="adrMailExp" required <?= isset($adrMailExp) ? "value=" . $adrMailExp : ""?> >
						<?php if( isset($erreurs['adrMailExp']) ) { echo "<aside>" . $erreurs['adrMailExp'] . "</aside>"; } ?>
						</div>
						<div class="champsForm">
							<label for="idMessage">Message <span>*</span></label>
									<textarea rows="4" minlength="<?= NB_CAR_MIN_MESSAGE_HTM ?>" maxlength="<?= NB_CAR_MAX_MESSAGE_HTM ?>" id="idMessage" name="message" required><?= isset($message) ? $message : ""?></textarea>
						<?php if( isset($erreurs['message']) ) { echo "<aside>" . $erreurs['message'] . "</aside>"; } ?>
						</div>

						<p><span>* saisie obligatoire</span></p>
						<div class="envoyer">
							<button name="bouton">Envoyer</button>
						</div>
					</form>
				</section>
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