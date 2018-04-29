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

require_once("./inclus/initDB.php");

$erreur = "";

if( isset( $_POST['connexion'] ) ) {

	$mail = $_POST['mail'];
	$password = $_POST['password'];

	// récupération du mot de passe crypté :
	$phraseRequete = "SELECT * FROM " . TABLE_CLIENTS . " WHERE mail = '" . $mail . "'";
	$requete = $dbConnex->prepare($phraseRequete);
	$requete->execute();
	$client = $requete->fetch();

	if($client){
		if( password_verify($password, $client['password']) ){

			// c'est le bon mot de passe, on ouvre la session :
			$_SESSION['client']['civilite'] = $client['civilite'];
			$_SESSION['client']['nom'] = $client['nom'];
			$_SESSION['client']['prenom'] = $client['prenom'];
			$_SESSION['client']['mail'] = $client['mail'];

			// on retourne à l'accueil :
			header("Location: index.php");
		}
		else{
			// c'est le mauvais mot de passe
			$erreur = "Aïe, erreur de connexion ...";
		}
	}
	else{
		// client inconnu
		$erreur = "Oups, erreur de connexion ...";
	}
}

// on détermine la page courante ...
// 1° => pour souligner le mot dans le menu de nav. : $pageCourante['flag']
// 2° => pour compléter le 'title' et le menu destinés à l'accessibilité : $pageCourante['nom']
$pageCourante = pageCourante($_SERVER['REQUEST_URI']);

?>

<!DOCTYPE html>
<html lang='fr'>
<head>
	<title><?= NOM_PHARMA . " - " . $pageCourante['nom'] ?></title>
	<meta charset='utf-8'>
	<meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= MC_CP ?>, <?= MC_1 ?>, <?= MC_2 ?>, <?= $pageCourante['nom'] ?>'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' rel='stylesheet' integrity='sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1' crossorigin='anonymous'>
	<link rel='stylesheet' type='text/css' href='../css/style.css'>
	<link rel='shortcut icon' href='../img/favicon.ico'>
</head>

<body>
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
			<a href='../index.php'>
				<img id='iLogoCroix' src='../img/croix_caducee.png' alt=''>
				<h1><?= NOM_PHARMA ?></h1>
				<h2><?= STI_PHARMA ?></h2>
			</a>
			<p id='iTelBandeau'><a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a><img class='cClicIndexTaille' src='img/icones/clicIndex.png' alt=''></p>
		</section>
		<nav id='iNavigation'>
			<ul>
				<li><a href='../index.php'   >Accueil </a></li>
				<li><a href='../horaires.php'>Horaires</a></li>
				<li><a href='../equipe.php'  >Équipe  </a></li>
				<li><a href='../contact.php' >Contact </a></li>
			</ul>
		</nav>
		<div id='iBandeauConnex'>
			<?php
				if( isset($_SESSION['client']) ){

					// si le client est connecté, on affiche son nom et le lien pour se déconnecter :
					echo "<div id='iClientConnecte'>";
						echo $_SESSION['client']['prenom'] . ' ' . $_SESSION['client']['nom'];
					echo "</div>";

					echo "<div id='iLienConnex'>";
						echo "<a href='deconnexion.php'>déconnexion</a>";
					echo "</div>";
				}
				else{

					// si le client n'est pas connecté, on affiche le lien pour se connecter :
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
		<section id='iConnexionIDs' class='cSectionContour'><h3>Veuillez saisir vos identifiants (*)</h3>

			<p class='cBraille'>
				(*) Si vous ne disposez pas encore d'identifiants, vous pouvez vous inscrire <a href='inscription.php'>ici.</a>
			</p>

			<p><?= ( ! empty($erreur) ) ? $erreur : "" ?></p>
			<form method='POST'>
				<div class='cChampForm'>
					<label for='iMail'>mail</label>
					<input type='text' id='iMail' name='mail' required autofocus>
				</div>

				<div class='cChampForm'>
					<label for='idPassword'>mot de passe</label>
					<input type='password' id='idPassword' name='password' required>
				</div>

				<div class='cBoutonOk'>
					<button name='connexion'>Connexion</button>
				</div>
			</form>
		</section>

		<section id='iConnexionInscription'  class='cSectionContour'><h3>(*) Création d'un compte</h3>
			<p>Si vous ne disposez pas encore d'identifiants, vous pouvez vous inscrire en suivant le lien : </p>
			<p><a href='inscription.php'>>  inscription  <</a></p>
		</section>
	</main>

	<?php include('footer.php'); ?>

</body>
</html>