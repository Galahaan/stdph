<?php

session_start(); // en début de chaque fichier utilisant $_SESSION

ini_set("display_errors", 1);  // sans doute à virer en prod, à vérifier  +++++++++++++++++++++++++++++       ++++++   +++++++   +++++++


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
// if( preg_match("admin", $page) ){                        ok en PHP 5.6.30 mais plus en PHP 7.1.4  ********************
if( strpos($page, "admin") ){
	echo "Vous n'avez pas accès à ce répertoire";
}
else{
    // On vérifie que la page est bien sur le serveur
    if (file_exists("include/" . $page) && $page != 'index.php') {
    	require_once("./include/".$page);
    }
    else{
    	echo "Erreur Include : le fichier " . $page . " est introuvable.";
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////
/////     FIN INCLUDE sécurisé
///////////////////////////////////////////////////////////////////////////////////////////////

require_onceCLR("constantes_CLRS");
// ici on est obligé d'utiliser la fonction native telle quelle, sinon elle ne peut pas jouer son rôle de "_once" :
require_once("./include/initDB.php");

// Si le formulaire vient d'être validé, et avant de savoir si on va
// sauvegarder les infos en BDD, on "nettoie" les champs :
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
	$nouveauCar = [' '];

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
	// str_replace : cf explications sur le remplacement de caractères ci-dessus
	$prenom = str_replace($trouverCar, $nouveauCar, $prenom);
	// enlève les espaces de début, fin, et les double-espaces en milieu de chaîne
	$prenom = SuperTrim($prenom);
	// 1ère lettre en majuscule, les autres en minuscules
	$prenom = ucfirst(strtolower($prenom));
	// test de la contrainte sur la longueur de la chaîne
	if( (strlen($prenom) < NB_CAR_MIN) || (strlen($prenom) > NB_CAR_MAX ) ){
		$erreurs['prenom'] = "(entre " . NB_CAR_MIN . " et " . NB_CAR_MAX . " caractères)";
	}

	//  ********  NOM  ********

	$nom = strip_tags($_POST['nom']);
	$nom = str_replace($trouverCar, $nouveauCar, $nom);
	$nom = SuperTrim($nom);
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
	}
	else{
		// si toutes les infos sont ok, il faudra créer un nouvel enregistrement, à la condition
		// que le mail, qui sert d'identifiant, ne soit pas déjà présent en BDD, d'où ce petit test :

		// requête pour interroger la BDD :
		$requete = $dbConnex->prepare("SELECT mail FROM clients WHERE mail = :mailB");
		$requete->bindValue("mailB", $adrMailClient, PDO::PARAM_STR);
		$requete->execute();
		$mailExisteDeja = $requete->fetchAll();
		print_r($mailExisteDeja);
	}

	//  ********  MOT DE PASSE  ********

	$password = $_POST['password'];
		if( (strlen($password) < NB_CAR_MIN_MESSAGE) || (strlen($password) > NB_CAR_MAX_MESSAGE ) ){
		$erreurs['password'] = "(entre " . NB_CAR_MIN_MESSAGE . " et " . NB_CAR_MAX_MESSAGE . " caractères)";
	}
	$passwordCrypte = password_hash($password, PASSWORD_DEFAULT);
}
?>

<!DOCTYPE html>
<html lang='fr'>
<head>
	<title>Pharmacie Le Reste</title>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' rel='stylesheet' integrity='sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1' crossorigin='anonymous'>
	<link rel='stylesheet' type='text/css' href='../css/style.css'>
	<link rel='shortcut icon' href='../img/favicon.ico'>
</head>

<body>
	<header>
		<section>
			<a href='../index.php'>
				<img src='../img/croix_mauve.png' alt=''>
				<h1>Pharmacie Le Reste</h1>
				<h2>Nantes, quartier Saint-Joseph de Porterie</h2>
			</a>
			<p id='iTelIndex'><i class='fa fa-volume-control-phone' aria-hidden='true'></i>&nbsp;&nbsp;<a href='tel:+33240251580'>02 40 25 15 80</a></p>
		</section>
		<nav class='cNavigation'>
			<ul>
				<li><a href='../index.php'   >Accueil </a></li>
				<li><a href='../horaires.php'>Horaires</a></li>
				<li><a href='../equipe.php' >Équipe  </a></li>
				<li><a href='../contact.php' >Contact </a></li>
			</ul>
		</nav>
		<div class='cBandeauConnex'>
			<?php
				if( isset($_SESSION['client']) ){

					// si le client est connecté, on affiche son nom et le lien pour se déconnecter :
					echo "<div class='cClientConnecte'>";
						echo $_SESSION['client']['prenom'] . " " . $_SESSION['client']['nom'];
					echo "</div>";

					echo "<div class='cLienConnex'>";
						echo "<a href='deconnexion.php'>déconnexion</a>";
					echo "</div>";
				}
				else{

					// si le client n'est pas connecté, on affiche le lien pour se connecter :
					echo "<div class='cClientConnecte'>";
						echo " ";
					echo "</div>";

					echo "<div class='cLienConnex'>";
						echo "<a href='connexion.php'>connexion</a>";
					echo "</div>";
				}
			?>
		</div>
	</header>

	<main>

		<?php if( isset($_POST['bouton']) && !isset($erreurs) && !$mailExisteDeja ) : ?>

			<?php
			//    le formulaire a été rempli  ET  il n'y a pas d'erreurs  ET  le mail n'existait pas encore en BDD

			// en plus des données du formulaire, on stocke la date de création :
			$dateCrea = date("d/m/Y - H\:i\:s");

			// requête pour créer un nouvel enregistrement :
			$requete = $dbConnex->prepare("INSERT INTO clients (dateCreation, civilite, nom, prenom, mail, password) VALUES (:dateB, :civiliteB, :nomB, :prenomB, :mailB, :passwordB)");

			$requete->bindValue("dateB", $dateCrea, PDO::PARAM_STR);
			$requete->bindValue("civiliteB", $civilite, PDO::PARAM_STR);
			$requete->bindValue("nomB", $nom, PDO::PARAM_STR);
			$requete->bindValue("prenomB", $prenom, PDO::PARAM_STR);
			$requete->bindValue("mailB", $adrMailClient, PDO::PARAM_STR);
			$requete->bindValue("passwordB", $passwordCrypte, PDO::PARAM_STR);
			$requete->execute();

			$nouvelId = $dbConnex->lastInsertId();
			echo "<article class='cArtiMessageConfirm'>";
			echo "<p>Merci, votre compte a bien été créé.</p>";
			echo "<p>Vous pouvez dorénavant vous connecter ...</p>";
			echo "<a href='connexion.php'>>  connexion  <</a>";
			echo "</article>";
			//header('Location: connexion.php');   ce serait pas mal de renvoyer vers la page connexion avec en paramètre le message de confirmation de création de compte.

			?>

		<?php else : ?>

			<?php

			// - soit il y a eu des erreurs dans le formulaire
			//   => alors on ré-affiche les valeurs saisies (grâce à "value"),
			//      ainsi qu'un message d'erreur pour les valeurs concernées.
			//
			// - soit le mail existait déjà en BDD
			//   => il faut re-proposer le formulaire comme dans le cas où
			//      il y a eu des erreurs
			//
			// - soit le formulaire n'a pas encore été rempli
			//   => on laisse les cases vides.
			?>

		<section class='cInscription'><h3>Création de votre compte</h3>
			<p>Veuillez renseigner tous les champs ci-dessous svp.</p>
			<form method='post'>
				<div class='cChampForm'>
					<input type='radio' id='iCiviliteMme' name='civilite' value='Mme' required
						<?= isset($civilite) && $civilite == "Mme" ? "checked" : ""?> >
					<label for='iCiviliteMme'>Mme</label>
					<input type='radio' id='iCiviliteMlle' name='civilite' value='Mlle' required
						<?= isset($civilite) && $civilite == "Mlle" ? "checked" : ""?> >
					<label for='iCiviliteMlle'>Melle</label>
					<input type='radio' id='iCiviliteM' name='civilite' value='M.' required
						<?= isset($civilite) && $civilite == "M." ? "checked" : ""?> >
					<label for='iCiviliteM'>M.</label>
				</div>

				<div class='cChampForm'>
					<label for='idPrenom'>Prénom</label>
					<input type='text' id='idPrenom' name='prenom' minlength='<?= NB_CAR_MIN_HTM ?>' maxlength='<?= NB_CAR_MAX_HTM ?>' required <?= isset($prenom) ? "value=" . $prenom : ""?> >
					<?php if( isset($erreurs['prenom']) ) { echo "<p><span>" . $erreurs['prenom'] . "</span></p>"; } ?>
				</div>

				<div class='cChampForm'>
					<label for='idNom'>Nom</label>
					<input type='text' id='idNom' name='nom' minlength='<?= NB_CAR_MIN_HTM ?>' maxlength='<?= NB_CAR_MAX_HTM ?>' required <?= isset($nom) ? "value=" . $nom : ""?> >
					<?php if( isset($erreurs['nom']) ) { echo "<p><span>" . $erreurs['nom'] . "</span></p>"; } ?>
				</div>

				<div class='cChampForm'>
					<label for='idMail'>Adresse mail</label>
					<input type='email' id='idMail' name='adrMailClient' required <?= isset($adrMailClient) ? "value=" . $adrMailClient : ""?> >
					<?php if( isset($erreurs['adrMailClient']) ) { echo "<p><span>" . $erreurs['adrMailClient'] . "</span></p>"; } ?>
					<?php if( $mailExisteDeja ) { echo "<p><span>Aïe, cet identifiant est déjà pris, veuillez en choisir un autre svp ...</span></p>"; } ?>
				</div>

				<div class='cChampForm'>
					<label for='idPassword'>Mot de passe</label>
					<input type='password' minlength='<?= NB_CAR_MIN_HTM ?>' maxlength='<?= NB_CAR_MAX_HTM ?>' id='idPassword' name='password' required <?= isset($password) ? $password : ""?> >
					<?php if( isset($erreurs['password']) ) { echo "<p><span>" . $erreurs['password'] . "</span></p>"; } ?>
				</div>

				<div class='cBoutonOk'>
					<button name='bouton'>Valider</button>
				</div>
			</form>
		</section>

		<?php endif ?>

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