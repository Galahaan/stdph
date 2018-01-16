<?php

session_start(); // en début de chaque fichier utilisant $_SESSION

ini_set("display_errors", 1);  // sans doute à virer en prod, à vérifier  +++++++++++++++++++++++++++++       ++++++   +++++++   +++++++

require_once("./include/initDB.php");

$erreur = "";

if( isset( $_POST['connexion'] ) ) {

	$mail = $_POST['mail'];
	$password = $_POST['password'];

	// récupération du mot de passe crypté :
	$requete = $dbConnex->prepare("SELECT * FROM clients WHERE mail = :mailB");
	$requete->bindValue("mailB", $mail, PDO::PARAM_STR);
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
			$erreur = "Erreur de connexion ...";
		}
	}
	else{
		// client inconnu
		$erreur = "Erreur de connexion ...";
	}
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
		<section class='cConnexion'><h3>Veuillez saisir vos identifiants (*)</h3>
			<p><?= ( ! empty($erreur) ) ? $erreur : "" ?></p>
			<form method='POST'>
				<div class='cChampForm'>
					<label for='idMail'>mail</label>
					<input type='text' id='idMail' name='mail' required>
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

		<section class='cConnexionInscription'><h3>(*) Création d'un compte</h3>
			<p>Si vous n'avez pas encore de compte, vous pouvez en créer un en suivant ce lien : </p>
			<p><a href='inscription.php'>>  inscription  <</a></p>
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