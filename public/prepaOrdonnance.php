<?php

// Si le nom de la page est saisi directement dans la barre d'adresse, alors
// que la personne ne s'est pas encore connectée => retour accueil direct !
session_start();
if( !isset($_SESSION['client']) ){
	header('Location: index.php');
}

include('inclus/entete.php');

// Pour des raisons de sécurité, dans le cas de l'envoi d'un mail, je teste si la page
// courante n'a pas été usurpée; je suis donc, das ce cas, obligé de l'écrire EN DUR :
// (et non pas, justement, en m'appuyant sur $_SERVER)
define("PAGE_EN_COURS", "prepaOrdonnance.php");

// Si le formulaire vient d'être validé, et avant de savoir si on va envoyer le mail, on "nettoie" les champs :
if( isset($_POST['valider']) ){

	// Civilité

	$civilite = $_POST['civilite'];

	// Prénom

	$prenomFiltre = filtrerPrenom($_POST['prenom']);
	$prenom = $prenomFiltre[0];
	if( isset($prenomFiltre[1]) ) $erreurs['prenom'] = $prenomFiltre[1];

	// Nom

	$nomFiltre = filtrerNom($_POST['nom']);
	$nom = $nomFiltre[0];
	if( isset($nomFiltre[1]) ) $erreurs['nom'] = $nomFiltre[1];

	// Mail

	$adrMailClient = $_POST['adrMailClient'];

	if( ! mailValide($adrMailClient) ){
		$erreurs['adrMailClient'] = "(mail invalide)"; 
	};

	// Message

	// on traite volontairement le cas du message AVANT celui de la pièce jointe pour
	// ne conserver la pièce jointe QUE si AUCUNE erreur n'a été détectée dans les tests.

	$messageClientTxt = chunk_split(htmlspecialchars(strip_tags($_POST['message'])));
	// NB: chunk_split est utilisée ici pour respecter la RFC 2045.
	//     utilisée de cette façon, sans param. optionnels, elle
	//     a pour rôle de scinder une chaîne, qui aurait été saisie
	//     d'un seul coup, sans retour chariot, en plusieurs lignes de 76 car. max.
	//     Mais si la chaîne fait moins de 76 car. au départ, chunk_split ajoute
	//     quand même un retour chariot, ce qui ajoute 2 caractères ('invisibles').

	if( (strlen($messageClientTxt) < NB_CAR_MIN_MESSAGE) || (strlen($messageClientTxt) > NB_CAR_MAX_MESSAGE ) ){
		$erreurs['message'] = "(de " . NB_CAR_MIN_MESSAGE . " à " . NB_CAR_MAX_MESSAGE . " caractères)";
	}
	// on se donne une version du message en format HTML (plus sympa à lire pour la pharmacie)
	$messageClientHtml = "<b style='font-size: 16px;'>" . nl2br($messageClientTxt) . "</b>";

	// Fichier joint

	$fichierInitial = $_FILES['pieceJointe'];
	$taille         = $fichierInitial['size']; 	   // en OCTETS
	$nomInitial     = $fichierInitial['name'];     // de la forme "fichier.txt"
	// on extrait l'extension (que l'on force en minuscules) :
	$extension      = strtolower( pathinfo($nomInitial, PATHINFO_EXTENSION) );
	$nomTemporaire  = $fichierInitial['tmp_name']; // de la forme "/tmp/phpxxxxxx", ie que cela inclut le chemin
	$type           = $fichierInitial['type'];
	// ex. image/gif ou image/jpeg ou application/pdf ou text/plain ou application/vnd.ms-excel ou application/octet-stream

	// => on vérifie que l'extension du fichier joint fait partie de celles autorisées,
	//    ET on prépare dès maintenant le "Content-type" de la pièce jointe du mail :
	switch ($extension) {
		case 'jpe' :
		case 'jpg' :
		case 'jpeg': $ContentType = "image/jpeg";      break;
		case 'png' : $ContentType = "image/png";       break;
		case 'gif' : $ContentType = "image/gif";       break;
		case 'pdf' : $ContentType = "application/pdf"; break;
		default:
			$erreurs['pieceJointe'] .= " [PJ extension invalide]";
	}


	// pour contrer au mieux les failles de sécurité, et s'il n'y a pas d'erreurs jusqu'ici,
	// on procède à une série de tests imbriqués portant sur la PJ, de façon à sortir de la série
	// dès la première erreur, et ne pas faire inutilement les tests suivants :
	if( ! isset($erreurs) ){

	// 1° => on choisit et on protège l'emplacement de stockage sur le serveur :
	//       (penser à bien définir les droits d'accès en lecture / écriture
	//       ainsi qu'un solide .htaccess qui interdira de voir l'index-of du répertoire en question)
		$repFinal = "../ordonnances_jointes";

	// 2° => on vérifie que la taille est bien positive mais ne dépasse pas X Mo :
		if( ! $taille > 0 ){
			$erreurs['pieceJointe'] .= " [PJ vide]";
		}
		else{

			if( $taille > TAILLE_MAX_PJ ){
				$erreurs['pieceJointe'] .= " [PJ trop volumineuse]";
			}
			else{

	// 3° => avant de stocker le fichier joint, s'il avait bien un nom, on lui en donne un nouveau,
	//          constitué de la date, du nom du client, suivi de caractères aléatoires :
				if( $nomInitial == "" ){
					$erreurs['pieceJointe'] .= " [PJ anonyme]";  // le fichier n'avait pas de nom :
				}
				else{

					// avant d'écrire la date dans le nom du fichier, on définit le fuseau horaire par défaut à utiliser :
					( date_default_timezone_set("Europe/Paris") ) ? $fuseau = "" : $fuseau = " (fuseau horaire invalide)";

					// si on veut que le nom de la PJ ne soit pas tronqué lors de l'envoi du mail,
					// il ne faut pas laisser d'espaces dedans :
					$prenomSE = str_replace(" ", "_", $prenom);
					$nomSE    = str_replace(" ", "_", $nom);
					$nouveauNom = date("Y-m-d__H-i-s__") . $prenomSE . "__" . $nomSE . "__" . bin2hex(random_bytes(16));

	// 4° => on vérifie qu'un fichier portant le même nom n'est pas déjà présent sur le serveur :
					if( file_exists($repFinal."/".$nouveauNom.".".$extension)) {
						// ce message là, qui ne devrait pas arriver, écrase les précédents :
						$erreurs['pieceJointe'] = "PJ - Erreur serveur, veuillez renvoyer le formulaire svp.";
					}
					else{

	// 5° => au final, comme il n'y a pas d'erreurs, alors on déplace le fichier à son emplacement définitif tout en le renommant :
						$succes = move_uploaded_file($nomTemporaire , $repFinal.'/'.$nouveauNom.'.'.$extension);

						if( ! $succes ){
							$erreurs['pieceJointe'] = "[PJ - erreur de transfert ". $_FILES['pieceJointe']['error']. "]";
							// la valeur de l'erreur renseigne sur sa signification :
							// 0 = a priori ce sont les droits d'accès en écriture qui ne sont pas conformes ...
							//	 ou des caractères non autorisés dans le nom du fichier : ex. "/" ...
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
				}
			}
		}
	}

	// si, à un quelconque endroit dans la série de tests ci-dessus,
	// il y a eu au moins une erreur, on supprime le fichier du serveur :
	if( isset($erreurs) ){
		    unlink($nomTemporaire);
	}
}
?>
	<main id='iMain'>
		<section id='iOrdoPrepaOrdo' class='cSectionContour'><h2>Préparation d'ordonnance</h2>
 
			<?php if( isset($_POST['valider']) && !isset($erreurs)) : ?>

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
					$date = "Semaine " . date("W") . " - " . dateFr() . " - " . heureActuelle(H);

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


					// =================  Objet du mail  ================= //

					// L'objet du message est constitué d'un préfixe (les 4 derniers car. de l'IP) suivi des prénom et nom de l'expéditeur :
					// (la fonction mb... sert à autoriser les caractères accentués)
					$objet = mb_encode_mimeheader("Ordonnance - [" .
													substr($ipClient, -4, 4) . "]  " .
													$civilite . " " .
													$prenom . " " .
													$nom, "UTF-8", "B");

					// =============  Création du séparateur  ============ //

					// ici il nous en faut 2 puisque nous devons séparer les 2 alternatives text : plain et html,
					// ainsi que le message proprement dit et la pièce jointe
					$boundary_A = md5(rand());
					$boundary_B = md5(rand());
					$separateur_A = $rc . "--" . $boundary_A . $rc; // on intègre les 2 "--" obligatoires et les CRLF
					$separateur_B = $rc . "--" . $boundary_B . $rc;

					// ==============  Création du header  =============== //

					// cf dossier "envoi de mails en PHP"
					$header =	"From: " .
								mb_encode_mimeheader(LABEL_EXP, "UTF-8", "B") .
								"<" . ADR_EXP_HBG . ">" . $rc .
								"Reply-To: $adrMailClient" . $rc .
								"MIME-Version: 1.0" . $rc .
								"X-Mailer: PHP/" . phpversion() . $rc .
								"Content-Type: multipart/mixed; boundary=" . $boundary_A;

					// =============== Création du message =============== //

					// Texte placé entre le header et le message proprement dit,
					// pour les clients mails ne supportant pas le type MIME (ça ne doit pas être très fréquent !..)
	           		$message = $rc . "Type MIME non pris en charge par votre client mail ..." . $rc;

					// on introduit les 2 versions alternatives :
					$message .=	$separateur_A . 
								"Content-Type: multipart/alternative; boundary=" . $boundary_B;

					// version "TEXT"
					$message .=	$separateur_B .
								"Content-Type: text/plain; charset='UTF-8'" . $rc .
								"Content-Transfer-Encoding: 8bit" . $rc .
								$date . " - " . $civilite . " " . $prenom . " " . $nom . "  -  " . $adrMailClient . $rc . $rc .
								$messageClientTxt . $rc . $rc. $rc . $rc .
								"IP  client     = " . $ipClient . $rc .
								"FAI client     = " . $faiClientBrut;

					// version "HTML"
					$message .=	$separateur_B .
								"Content-Type: text/html; charset='UTF-8'" . $rc .
								"Content-Transfer-Encoding: 8bit" . $rc .
								$date . " - <b>" . $civilite . " " . $prenom . " " . $nom . "</b>  -  " . $adrMailClient . "<br><br>" .
								$messageClientHtml . "<br>" .
								"IP  client     = " . $ipClient . "<br>" .
								"FAI client     = " . $faiClientBrut;

					// ========== Insertion de la pièce jointe =========== //

					// 1- on ouvre le fichier en lecture seule :
					$flux = fopen($repFinal."/".$nouveauNom.".".$extension, "r") or die("impossible à ouvrir !");

					// 2- on parcourt l'ensemble du fichier :
					$pieceJointe = fread( $flux, filesize($repFinal."/".$nouveauNom.".".$extension) );

					// 3- on referme le fichier :
					fclose($flux);

					// 4- on encode la pièce jointe :
					$pieceJointe = chunk_split(base64_encode($pieceJointe));

					// 5- on ajoute la PJ dans le mail :
					//    (le Content-type a été préparé au moment des vérifications sur la pièce jointe)
					$message .= $separateur_A .
								"Content-Type: " . $ContentType . "; name=" . $nouveauNom . "." . $extension . $rc . // ex. "image/jpeg"
								"Content-Transfer-Encoding: base64" . $rc .
								"Content-Disposition: attachment; filename=" . $nouveauNom . "." . $extension . $rc .
								$pieceJointe;

					// ================== Envoi du mail ================== //

					// En réalité, il faut envisager la préparation de 2 mails différents,
					// l'un dans le cas 'normal', et l'autre dans le cas où un 'pirate'
					// voudrait envoyer le mail à partir du site de son choix.
					// 
					// Mais dans les 2 cas, on enverra la même confirmation d'envoi du mail,
					// d'où le stockage de cette confirmation dans une variable (bon 2 en fait !) :

					// on effacera le titre de la page : "Préparation d'ordonnance"
					// puisque le message est suffisamment explicite :
					$effaceContenuPage =
						"<style type='text/css'>" .
								"#iOrdoPrepaOrdo h2 { display: none }" .
						"</style>";

					// puis on affichera le message de confirmation
					//
					// NB: pour le braille, on positionne le focus
					//     (comme le mot clé HTML5 'autofocus' ne fonctionne que sur des balises de type <input>,
					//      on utilise du javascript)
					$messageConfirmation =
						"<div class='cMessageConfirmation'>" .
								"<p id='iFocus'>Merci, votre ordonnance a bien été envoyée.</p>" .
								"<p>Nous vous répondrons dans les meilleurs délais, " .
									"sous réserve qu'il n'y ait pas d'erreur dans l'adresse mail fournie.</p>" .
						"</div>";

					$messageConfirmationErreur =
						"<div class='cMessageConfirmation'>" .
								"<p id='iFocus'>Aïe, il y a eu un problème ...</p>" .
								"<p>Le serveur est probablement indisponible, veuillez réessayer ultérieurement, merci.</p>" .
						"</div>";

					// Donc, 1er cas : tentative de piratage :
					// si le formulaire n'est pas posté de notre site, on envoie un mail avec un avertissement :
					if(    strcmp( $_SERVER['HTTP_REFERER'], ADRESSE_SITE_PHARMACIE . PAGE_EN_COURS ) != 0
						&& strcmp( $_SERVER['HTTP_REFERER'], S_ADRESSE_SITE_PHARMACIE . PAGE_EN_COURS ) != 0
						&& strcmp( $_SERVER['HTTP_REFERER'], W_ADRESSE_SITE_PHARMACIE . PAGE_EN_COURS ) != 0
						&& strcmp( $_SERVER['HTTP_REFERER'], SW_ADRESSE_SITE_PHARMACIE . PAGE_EN_COURS ) != 0 ){

						$headerAlerte =	"From: " .
										mb_encode_mimeheader(LABEL_EXP_PIRATE, "UTF-8", "B") .
										"<" . ADR_EXP_HBG . ">" . $rc .
										"Reply-To: " . $rc .
										"MIME-Version: 1.0" . $rc .
										"X-Mailer: PHP/" . phpversion() . $rc .
										"Content-Type: text/html; charset='UTF-8'" . $rc .
										"Content-Transfer-Encoding: 8bit";
						$messageAlerte =	"<br><b>&nbsp;&nbsp;ATTENTION !<br>" .
											"Ce formulaire a été envoyé à partir d'un site web DIFFERENT de celui de la pharmacie : " . "<br>" .
											$_SERVER['HTTP_REFERER'] . "</b><br>" .
											"(par sécurité, la pièce jointe a été supprimée de cet envoi)<br>" .
											"_______________________________________________________________________________" . "<br><br>" .
											$date . " - " . $civilite . " " . $prenom . " " . $nom . "  -  " . $adrMailClient . "<br><br>" .
											$messageClientTxt . "<br><br>" .
											"IP  client     = " . $ipClient . "<br>" .
											"FAI client     = " . $faiClientBrut;
						if( mail(MAIL_DEST_PHARMA, "Ordonnance - Tentative de piratage ?", $messageAlerte, $headerAlerte) ){
							echo $effaceContenuPage;
							echo $messageConfirmation;
						}
						else{
							// ici, on n'efface pas le titre de la page, pour savoir de quoi parle le message d'erreur
							echo $messageConfirmationErreur;
						};
					} 
					else{

					    // 2ème cas : envoi de l'e-mail 'normal' :

						if( mail(MAIL_DEST_PHARMA, $objet, $message, $header) ){
							echo $effaceContenuPage;
							echo $messageConfirmation;
						}
						else{
							// ici, on n'efface pas le titre de la page, pour savoir de quoi parle le message d'erreur
							echo $messageConfirmationErreur;
						}
					}; 
					?>

			<?php else : ?>

				<?php

				// - soit le formulaire n'a pas encore été rempli :
				//        => on pré-remplit les champs avec les données de session
				//			 (mais si le formulaire a déjà été rempli, on ne modifie pas les valeurs saisies, d'où le if)

				if( ! isset( $civilite )		){		$civilite		= $_SESSION['client']['civilite'];		};
				if( ! isset( $nom )				){		$nom			= $_SESSION['client']['nom'];			};
				if( ! isset( $prenom )			){		$prenom			= $_SESSION['client']['prenom'];		};
				if( ! isset( $adrMailClient )	){		$adrMailClient	= $_SESSION['client']['mail'];			};

				// - soit il y a eu des erreurs dans le formulaire :
				//   => alors on ré-affiche les valeurs saisies (grâce à "value"),
				//		ainsi qu'un message d'erreur pour les valeurs concernées,
				//		le tout en activant l'autofocus, pour se déplacer
				//		automatiquement sur le 1e champ en erreur.

				// Si jamais il y a plusieurs erreurs, on ne placera le focus que sur la 1ère,
				// d'où l'utilisation de ce booleen :
				$focusErreurMis = false;
				?>

				<div id='iBlablaIntro'>
					<p>Envoyez-nous votre ordonnance via le formulaire ci-dessous.</p>
					<p>Les produits seront alors aussitôt préparés et vous serez prévenu(e) par mail de leur mise à disposition.</p>
					<p><span class='cCouleurRouge'>Attention</span>, venez à la pharmacie <span class='cCouleurRouge'>avec l'original de l'ordonnance</span>.</p>
					<p>Pensez aussi à la <span class='cCouleurRouge'>carte vitale</span> et à la <span class='cCouleurRouge'>carte de mutuelle</span>.</p>
					<p>Si tous les produits sont en stock, le délai moyen de préparation est d'environ 2 h, sinon une demi-journée suffit en général.</p>
				</div>

				<article>
					<p id='iOrdoLienModeEmploi'>
						<a href='#iOrdoModeEmploi'>Mode d'emploi</a>&nbsp;&nbsp;<img class='cClicIndexTaille' src='img/icones/clicIndex.png' alt=''>
						<a class='cBraille'>fin du mode d'emploi</a>
					</p>
					<div id='iOrdoModeEmploi'>
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
				<sup>Veuillez renseigner tous les champs ci-dessous svp.</sup>
				<sup>(pièce jointe < <?= TAILLE_MAX_PJ / 1024 / 1024 ?> Mo)</sup>
				<form method='POST' enctype='multipart/form-data'>
					<div class='cChampForm'>
						<input type='radio' id='iCiviliteMme'  name='civilite' value='Mme'  required
							<?= $civilite == "Mme"  ? "checked" : ""?> >
						<label for='iCiviliteMme' >Mme</label>
						<input type='radio' id='iCiviliteMlle' name='civilite' value='Mlle' required
							<?= $civilite == "Mlle" ? "checked" : ""?> >
						<label for='iCiviliteMlle'>Melle</label>
						<input type='radio' id='iCiviliteM'    name='civilite' value='M.'   required
							<?= $civilite == "M."   ? "checked" : ""?> >
						<label for='iCiviliteM'   >M.</label>
					</div>
					<div class='cChampForm'>
						<label for='iPrenom'>Prénom</label>
								<input type='text' id='iPrenom' name='prenom' minlength='<?= NB_CAR_MIN_HTM ?>' maxlength='<?= NB_CAR_MAX_HTM ?>' required <?= isset($prenom) ? 'value="' . $prenom . '"' : "" ?>
									<?php	if( isset($erreurs['prenom']) && $focusErreurMis == false ){
												echo " autofocus";
												$focusErreurMis = true;
											}
									?>
								>
						<?php if( isset($erreurs['prenom']) ) { echo "<sub>" . $erreurs['prenom'] . "</sub>"; } ?>
					</div>
					<div class='cChampForm'>
						<label for='iNom'>Nom</label>
								<input type='text' id='iNom' name='nom' minlength='<?= NB_CAR_MIN_HTM ?>' maxlength='<?= NB_CAR_MAX_HTM ?>' required <?= isset($nom) ? 'value="' . $nom . '"' : ""?>
									<?php	if( isset($erreurs['nom']) && $focusErreurMis == false ){
												echo " autofocus";
												$focusErreurMis = true;
											}
									?>
								>
						<?php if( isset($erreurs['nom']) ) { echo "<sub>" . $erreurs['nom'] . "</sub>"; } ?>
					</div>
					<div class='cChampForm'>
						<label for='iMail'>Mail</label>
								<input type='email' id='iMail' name='adrMailClient' required <?= isset($adrMailClient) ? "value=" . $adrMailClient : ""?>
									<?php	if( (isset($erreurs['adrMailClient']) || isset($erreurs['pieceJointe'])) && $focusErreurMis == false ){
												echo " autofocus";
												$focusErreurMis = true;
											}
									?>
								>
						<?php if( isset($erreurs['adrMailClient']) ) { echo "<sub>" . $erreurs['adrMailClient'] . "</sub>"; } ?>
					</div>
					<div class='cChampForm'>
						<label for='iPJ'>Ordonnance</label>
								<input type='file' id='iPJ' name='pieceJointe' accept=<?= LISTE_EXT_AUTORISEES ?> required >
								<?php // visiblement l'autofocus n'a pas d'effet sur un <input type='file'>, donc on le met ci-dessus :( ?>
						<?php if( isset($erreurs['pieceJointe']) ) { echo "<sub>" . $erreurs['pieceJointe'] . "</sub>"; } ?>
					</div>
					<div class='cChampForm'>
						<label for='iMessageTextarea'>Message</label>
								<p>Apportez-nous des précisions qui vous semblent utiles sur votre traitement.
								<br>Peut-être avez-vous déjà certains produits qu'il serait donc inutile d'ajouter à la préparation ?..</p>
								<textarea rows='8' minlength='<?= NB_CAR_MIN_MESSAGE_HTM ?>' maxlength='<?= NB_CAR_MAX_MESSAGE_HTM ?>' id='iMessageTextarea' name='message' required placeholder='...'
									<?php	if( isset($erreurs['message']) && $focusErreurMis == false ) {
												echo " autofocus";
												$focusErreurMis = true;
											}
									?>
								><?= isset($messageClientTxt) ? $messageClientTxt : "" ?></textarea>
						<?php if( isset($erreurs['message']) ) { echo "<sub>" . $erreurs['message'] . "</sub>"; } ?>
					</div>
					<div id='iValider'>
						<button class='cDecoBoutOK' name='valider'>Envoyer</button>
					</div>
				</form>
			<?php endif ?>
		</section>
	</main>

	<?php include('inclus/pdp.php'); ?>

	<script src='scriptsJs/scripts.js'></script>

</body>
</html>