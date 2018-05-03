<?php

include('inclus/entete.php');

// ici on est obligé d'utiliser la fonction native telle quelle, sinon elle ne peut pas jouer son rôle de "_once" :
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
			// A noter : ici, la fonction header fonctionne bien parce qu'on est bien au dessus
			//           du DOCTYPE et que la page HTML n'a pas encore commencé à être chargée.
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
			<p>(La CNIL protège vos données, cf mentions légales ci-dessous)</p>
			<p><a href='inscription.php'>>  inscription  <</a></p>
		</section>
	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>