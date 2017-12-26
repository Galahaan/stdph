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
	    if (file_exists("include/" . $page) && $page != 'index.php') {
	    	include_once("./include/".$page);
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
	// page en cours :
	define("PAGE_EN_COURS", "prepaCommande.php");

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
		// remplace les espaces ' ' par des soulignés '_'
		$prenom = str_replace(" ", "_", $prenom);
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
		$nom = str_replace(" ", "_", trim($nom));
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
		<section class="formComm"><h3>Préparation de commande</h3>
 
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

					// au début j'avais fait ça :
					// $date = date('\S\e\m. W - D d/m/Y - H:i:s') . $fuseau;
					// mais c'était en anglais, alors j'ai voulu utiliser 'locale' :
					// setlocale(LC_TIME, "fra");
					// $date = "Semaine " . strftime('%W - %A %d/%B/%Y - %H:%M:%S') . $fuseau;
					// sauf que comme on est sur un serveur mutualisé, on ne peut pas modifier 'locale', donc ça restait en anglais !
					//
					// d'où l'utilisation d'une fonction à moi :
					$date = 'Semaine ' . date('W') . ' - ' . dateFr();

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
					$objet = mb_encode_mimeheader("Commande - [" .
													substr($ipClient, -4, 4) . "]  " .
													$civilite . " " .
													$prenom . " " .
													$nom, "UTF-8", "B");

					// ============  Création du header  ============ //

					// cf dossier "envoi de mails en PHP"
					$header =	"From: " .
								mb_encode_mimeheader(LABEL_EXP, "UTF-8", "B") .
								"<" . ADR_EXP_HEBERGEUR . ">" . $rc .
								"Reply-To: $adrMailClient" . $rc .
								"MIME-Version: 1.0" . $rc .
								"X-Mailer: PHP/" . phpversion() . $rc .
								"Content-type: text/html; charset=UTF-8" . $rc .
			           			"Content-Transfer-Encoding: 8bit";

					// ============= Création du message ============= //

					// version HTML seule
					$message =	$date . " - <b>" . $civilite . " " . $prenom . " " . $nom . "</b>  -  " . $adrMailClient . "<br>" . "<br>" .
								$messageClientHtml . "<br><br><br><br>" .
								"IP  client     = " . $ipClient . "<br>" .
								"FAI client     = " . $faiClientBrut;

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
							echo "<p>Merci, votre commande a bien été envoyée.</p>";
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
					<p>Envoyez-nous votre commande via le formulaire ci-dessous.</p>
					<p>Écrivez librement les produits dont vous avez besoin.</p>
					<p>Seuls les produits ne nécessitant pas d'ordonnance médicale peuvent être commandés.</p>
					<p>Vous serez prévenu(e) par mail dès la mise à disposition de votre commande.</p>
					<p>Si tous les produits sont en stock, le délai moyen de préparation est d'environ 2 h, sinon une demi-journée suffit en général.</p>
				</div>

				<span class="obligatoire">(la saisie de tous les champs est obligatoire)</span>
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
						<label for="idMessage">Commande</label>
								<textarea rows="8" minlength="<?= NB_CAR_MIN_MESSAGE_HTM ?>" maxlength="<?= NB_CAR_MAX_MESSAGE_HTM ?>" id="idMessage" name="message" required><?= isset($messageClientTxt) ? $messageClientTxt : ""?></textarea>
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