<?php

session_start(); // en début de chaque fichier utilisant $_SESSION
ini_set("display_errors", 1);  // affichage des erreurs - à virer à la mise en prod !

///////////////////////////////////////////////////////////////////////////////////////////////
/////     INCLUDE sécurisé
///////////////////////////////////////////////////////////////////////////////////////////////

if( empty($page) ){
$page = "fonctions"; // page à inclure : fonctions.php qui lui-même inclut constantes.php

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
// if( preg_match("admin", $page) ){
if( strpos($page, "admin") ){
	echo "Vous n'avez pas accès à ce répertoire";
}
else{
    // On vérifie que la page est bien sur le serveur
    if (file_exists("inclus/" . $page) && $page != "index.php") {
    	require_once("./inclus/".$page);
    }
    else{
    	echo "Erreur Include : le fichier " . $page . " est introuvable.";
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////
/////     FIN INCLUDE sécurisé
///////////////////////////////////////////////////////////////////////////////////////////////

// ici on est obligé d'utiliser la fonction native telle quelle, sinon elle ne peut pas jouer son rôle de "_once" :
require_once("./inclus/initDB.php");

// on détermine la page courante ...
// 1° => pour souligner le mot dans le menu de nav. : $pageCourante['flag']
// 2° => pour compléter le 'title' et le menu destinés à l'accessibilité : $pageCourante['nom']
$pageCourante = pageCourante($_SERVER['REQUEST_URI']);

// Si le formulaire vient d'être validé, et avant de savoir si on va
// sauvegarder les infos en BDD, on "nettoie" les champs :
if( isset($_POST['bouton']) ){

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

	// "nettoie" la valeur utilisateur :
	$adrMailClient = filter_var($_POST['adrMailClient'], FILTER_SANITIZE_EMAIL);

	// teste la NON validité du format :
	if( ! filter_var($adrMailClient, FILTER_VALIDATE_EMAIL) ){
		$erreurs['adrMailClient'] = "(format incorrect)"; 
	}
	else{
		// si toutes les infos sont ok, il faudra créer un nouvel enregistrement, à la condition
		// que le mail, qui sert d'identifiant, ne soit pas déjà présent en BDD, d'où ce petit test :

		// requête pour interroger la BDD :

		// au début, je faisais ça, et ça marchait très bien, tant que le nom de la table
		// était écrit en "dur" :
		// le pb, c'est qu'on veut stocker le nom de la table dans une constante d'un fichier de config ...
		// mais en utilisant la même technique pour le nom de la table que pour les valeurs des champs,
		// ie avec le bindValue, ça ajoute des guillemets autour du nom de la table ... et ça, ça ne passe pas en SQL !
		// (mais il en faut autour des valeurs des champs)

		// $requete = $dbConnex->prepare("SELECT mail FROM clients WHERE mail = :mailB");
		// $requete->bindValue("mailB", $adrMailClient, PDO::PARAM_STR);

		// d'où la solution : construire une chaîne de caractères complète, avec des guillemets là où il en faut !
		$phraseRequete = "SELECT mail FROM " . TABLE_CLIENTS . " WHERE mail = '" . $adrMailClient . "'";
		$requete = $dbConnex->prepare($phraseRequete);
		$requete->execute();
		$mailExisteDeja = $requete->fetchAll();
	}

	// Mot de passe :

	$password = $_POST['password'];
		if( (strlen($password) < NB_CAR_MIN_MDP) || (strlen($password) > NB_CAR_MAX_MDP ) ){
		$erreurs['password'] = "(entre " . NB_CAR_MIN_MDP . " et " . NB_CAR_MAX_MDP . " caractères)";
	}
	$passwordCrypte = password_hash($password, PASSWORD_DEFAULT);
}
?>

<!DOCTYPE html>
<html lang='fr'>
<head>
	<title><?= NOM_PHARMA . " - " . $pageCourante['nom'] ?></title>
	<meta charset='utf-8'>
	<meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= MC_CP ?>, <?= MC_1 ?>, <?= MC_2 ?>, <?= $pageCourante['nom'] ?>'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' rel='stylesheet' integrity='sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1' crossorigin='anonymous'>
	<link rel='stylesheet' type='text/css' href='css/style.css'>
	<link rel='shortcut icon' href='img/favicon.ico'>
</head>

<body onload='placerFocus("iFocus")'>
	<header>
		<nav class='cBraille'><?= $pageCourante['nom'] ?>
			<ol>
				<li><a href='aide.php'     accesskey='h'>[h] Aide à la navigation dans le site</a></li>
				<li><a href='#iNavigation' accesskey='n'>[n] Menu de navigation</a></li>
				<li><a href='#iLienConnex' accesskey='c'>[c] Connexion/Inscription/Deconnexion</a></li>
				<li><a href='#iMain'       accesskey='m'>[m] contenu de <?= $pageCourante['nom'] ?></a></li>
			</ol>
		</nav>

		<section>
			<a href='index.php' accesskey='r'>
				<img id='iLogoCroix' src='img/croix_caducee.png' alt=''>
				<h1><?= NOM_PHARMA ?></h1>
				<h2><?= STI_PHARMA ?></h2>
			</a>
			<p id='iTelBandeau'><a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a><img class='cClicIndexTaille' src='img/clicIndex.png' alt=''></p>
		</section>
		<nav id='iNavigation'>
			<ul>
				<li><a <?= ($pageCourante['flag'] == "1000") ? "id = 'iPageCourante'" : "" ?> href='index.php'   >Accueil </a></li>
				<li><a <?= ($pageCourante['flag'] == "0100") ? "id = 'iPageCourante'" : "" ?> href='horaires.php'>Horaires</a></li>
				<li><a <?= ($pageCourante['flag'] == "0010") ? "id = 'iPageCourante'" : "" ?> href='equipe.php'  >Équipe  </a></li>
				<li><a <?= ($pageCourante['flag'] == "0001") ? "id = 'iPageCourante'" : "" ?> href='contact.php' >Contact </a></li>
			</ul>
		</nav>
		<div id='iBandeauConnex'>
			<?php
				if( isset($_SESSION['client']) ){

					// si le client est connecté, on affiche son nom et le lien pour se déconnecter :
					echo "<div id='iClientConnecte'>";
						echo $_SESSION['client']['prenom'] . " " . $_SESSION['client']['nom'];
					echo "</div>";

					echo "<div id='iLienConnex'>";
						echo "<a href='deconnexion.php'>déconnexion</a>";
					echo "</div>";
				}
				else{

					// si le client n'est pas connecté, (normalement c'est impossible d'arriver là
					// sans être connecté) on affiche le lien pour se connecter :
					echo "<div id='iClientConnecte'>";
						echo " ";
					echo "</div>";

					echo "<div id='iLienConnex'>";
						echo "<a href='connexion.php'>connexion</a>";
					echo "</div>";
				}
			?>
		</div>
	</header>

	<main id='iMain'>
		<nav class='cBraille'>
			<ol>
				<li><a href="#iContactInfosPratiques">Informations pratiques</a></li>
				<li><a href="#iContactCoordonnees">Coordonnées de la <?= NOM_PHARMA ?></a></li>
				<li><a href="#iContactPlan">Localiser la <?= NOM_PHARMA ?></a></li>
				<li><a href="#iContactFormulaire">Formulaire de contact</a></li>
			</ol>
		</nav>

		<section id='iInscription' class='cSectionContour'><h3>Création de votre compte</h3>

		<?php if( isset($_POST['bouton']) && !isset($erreurs) && !$mailExisteDeja ) : ?>

			<?php
			//    le formulaire a été rempli  ET  il n'y a pas d'erreurs  ET  le mail n'existait pas encore en BDD

			// en plus des données du formulaire, on stocke la date de création :
			$dateCrea = date("d/m/Y - H\:i\:s");

			// requête pour créer un nouvel enregistrement :

			// au début, je faisais ça, et ça marchait très bien, tant que le nom de la table
			// était écrit en "dur" :
			// le pb, c'est qu'on veut stocker le nom de la table dans une constante d'un fichier de config ...
			// mais en utilisant la même technique pour le nom de la table que pour les valeurs des champs,
			// ie avec le bindValue, ça ajoute des guillemets autour du nom de la table ... et ça, ça ne passe pas en SQL !
			// (mais il en faut autour des valeurs des champs)

			// $requete = $dbConnex->prepare("INSERT INTO clients (dateCreation, civilite, nom, prenom, mail, password) VALUES (:dateB, :civiliteB, :nomB, :prenomB, :mailB, :passwordB)");
			// $requete->bindValue("dateB", $dateCrea, PDO::PARAM_STR);
			// $requete->bindValue("civiliteB", $civilite, PDO::PARAM_STR);
			// $requete->bindValue("nomB", $nom, PDO::PARAM_STR);
			// $requete->bindValue("prenomB", $prenom, PDO::PARAM_STR);
			// $requete->bindValue("mailB", $adrMailClient, PDO::PARAM_STR);
			// $requete->bindValue("passwordB", $passwordCrypte, PDO::PARAM_STR);

			// d'où la solution : construire une chaîne de caractères complète, avec des guillemets là où il en faut !
			// (avant je délimitais les ch. de car. de la requête par des " et les variables par des ' mais
			//  j'ai dû inverser le jour où j'ai décidé d'accepter le car. ' dans les noms : ex. Mc Kulloc'h )
			$phraseRequete = 'INSERT INTO ' . TABLE_CLIENTS .
							 ' (dateCreation, civilite, nom, prenom, mail, password) VALUES ("' .
							 $dateCrea . '", "' .
							 $civilite . '", "' .
							 $nom . '", "' .
							 $prenom . '", "' .
							 $adrMailClient . '", "' .
							 $passwordCrypte . '")';
			$requete = $dbConnex->prepare($phraseRequete);
			$requete->execute();

			$nouvelId = $dbConnex->lastInsertId();
			echo "<div class='cMessageConfirmation'>";
				// NB: pour le braille, on positionne le focus (merci HTML5 !) comme ça ils n'ont pas à relire tout le début de la page pour accéder au message de confirmation.
			echo "<p id='iFocus'>Merci, votre compte a bien été créé.</p>";
			echo "<p>Vous pouvez dorénavant vous connecter ...</p>";
			echo "<a href='connexion.php'>>  connexion  <</a>";
			echo "</div>";

			?>

		<?php else : ?>

			<?php

			// - soit il y a eu des erreurs dans le formulaire
			//   => alors on ré-affiche les valeurs saisies (grâce à "value"),
			//      ainsi qu'un message d'erreur pour les valeurs concernées,
			//      le tout en activant l'autofocus, pour se déplacer
			//      automatiquement jusqu'au formulaire.
			//
			// - soit le mail existait déjà en BDD
			//   => il faut re-proposer le formulaire comme dans le cas où
			//      il y a eu des erreurs
			//
			// - soit le formulaire n'a pas encore été rempli
			//   => on laisse les cases vides.

			// Si jamais il y a plusieurs erreurs, on ne placera le focus que sur la 1ère,
			// d'où l'utilisation de ce booleen :
			$focusErreurMis = false;
			?>

			<sup>Veuillez renseigner tous les champs ci-dessous svp.</sup>
			<form method='POST'>
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
						<input type='text' id='iPrenom' name='prenom' minlength='<?= NB_CAR_MIN_HTM ?>' maxlength='<?= NB_CAR_MAX_HTM ?>' required <?= isset($prenom) ? 'value="' . $prenom . '"' : ""?>
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
					<label for='iMail'>Adresse mail</label>
						<input type='email' id='iMail' name='adrMailClient' required <?= isset($adrMailClient) ? "value=" . $adrMailClient : ""?>
							<?php	if( isset($erreurs['adrMailClient']) && $focusErreurMis == false ){
										echo " autofocus";
										$focusErreurMis = true;
									}
							?>
						>
					<?php if( isset($erreurs['adrMailClient']) ) { echo "<sub>" . $erreurs['adrMailClient'] . "</sub>"; } ?>
					<?php if( $mailExisteDeja ) { echo "<sub>Aïe, cet identifiant est déjà pris, veuillez en choisir un autre svp ...</sub>"; } ?>
				</div>
				<div class='cChampForm'>
					<label for='idPassword'>Mot de passe</label>
						<input type='password' id='idPassword' name='password' minlength='<?= NB_CAR_MIN_MDP_HTM ?>' maxlength='<?= NB_CAR_MAX_MDP_HTM ?>' required
							<?php	if( isset($erreurs['password']) && $focusErreurMis == false ){
										echo " autofocus";
										$focusErreurMis = true;
									}
							?>
						>
					<?php if( isset($erreurs['password']) ) { echo "<sub>" . $erreurs['password'] . "</sub>"; } ?>
				</div>
				<div class='cBoutonOk'>
					<button name='bouton'>Valider</button>
				</div>
			</form>
		</section>

		<?php endif ?>

	</main>

	<footer>
		<section><h3>Coordonnées de la <?= NOM_PHARMA ?></h3>
			<p><?= NOM_PHARMA ?></p>
			<p><?= ADR_PHARMA_L1 ?></p>
			<p><?= CP_PHARMA ?> <?= VIL_PHARMA ?></p>
			<p>tel - <?= TEL_PHARMA_DECO ?></p>
			<p>fax - <?= FAX_PHARMA_DECO ?></p>
		</section>
		<section><h3>Informations sur l'editeur du site</h3>
			<p>Édition CLR - 2018</p>
		</section>
	</footer>
	<script src='scriptsJs/scripts.js' type='text/javascript'></script>
</body>
</html>