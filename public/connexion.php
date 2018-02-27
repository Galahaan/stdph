<?php

session_start(); // en début de chaque fichier utilisant $_SESSION

ini_set("display_errors", 1);  // affichage des erreurs - à virer à la mise en prod !

require_once("./include/initDB.php"); // initDB.php inclut constantes.php

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
?>

<!DOCTYPE html>
<html lang='fr'>
<head>
	<title><?= NOM_PHARMA ?></title>
	<meta charset='utf-8'>
	<meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= MC_CP ?>, <?= MC_1 ?>, <?= MC_2 ?>'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' rel='stylesheet' integrity='sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1' crossorigin='anonymous'>
	<link rel='stylesheet' type='text/css' href='../css/style.css'>
	<link rel='shortcut icon' href='../img/favicon.ico'>
</head>

<body>
	<header>
		<section>
			<a href='../index.php'>
				<img id='iLogoCroix' src='../img/croix_caducee.png' alt=''>
				<h1><?= NOM_PHARMA ?></h1>
				<h2><?= STI_PHARMA ?></h2>
			</a>
			<p id='iTelBandeau'><a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a><img class='cClicIndexTaille' src='img/clicIndex.png' alt=''></p>
		</section>
		<nav class='cNavigation'>
			<ul>
				<li><a href='../index.php'   >Accueil </a></li>
				<li><a href='../horaires.php'>Horaires</a></li>
				<li><a href='../equipe.php'  >Équipe  </a></li>
				<li><a href='../contact.php' >Contact </a></li>
			</ul>
		</nav>
		<div class='cBandeauConnex'>
			<?php
				if( isset($_SESSION['client']) ){

					// si le client est connecté, on affiche son nom et le lien pour se déconnecter :
					echo "<div class='cClientConnecte'>";
						echo $_SESSION['client']['prenom'] . ' ' . $_SESSION['client']['nom'];
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
		<section id='iConnexionIDs' class='cSectionContour'><h3>Veuillez saisir vos identifiants (*)</h3>
			<p><?= ( ! empty($erreur) ) ? $erreur : "" ?></p>
			<form method='POST'>
				<div class='cChampForm'>
					<label for='iMail'>mail</label>
					<input type='text' id='iMail' name='mail' required>
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
			<p>Si vous n'avez pas encore de compte, vous pouvez en créer un en suivant ce lien : </p>
			<p><a href='inscription.php'>>  inscription  <</a></p>
		</section>
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
</body>
</html>