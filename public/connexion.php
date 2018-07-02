<?php

include('inclus/entete.php');

// ici on est obligé d'utiliser la fonction native telle quelle, sinon elle ne peut pas jouer son rôle de "_once" :
require_once("./inclus/initDB.php");

$erreur = "";

if( isset( $_POST['valider'] ) ) {

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
			$_SESSION['client']['tel'] = $client['tel'];

			// juste avant de retourner à l'accueil, on stocke en BDD la date de cette connexion
			// => c'est la 1ère étape pour respecter la déclaration à la CNIL sur la durée de stockage des données
			//    (la 2e étape consistera à détruire ces données quand elles auront dateConx + 1 an)
			$phraseRequete = "UPDATE ". TABLE_CLIENTS . " SET dateConx = '" . date('Y-m-d') .  "' WHERE id = " . $client['id'];
			$requete = $dbConnex->prepare($phraseRequete);
			if( $requete->execute() != true ){ $erreurRequete = true; }
			//pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...

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
		<section id='iConnexionIDs' class='cSectionContour'>
			<p class='cInLi'>Veuillez saisir vos identifiants de&nbsp;</p><h2 class='cInLi'>connexion</h2><p class='cInLi'>&nbsp;(*)</p>

			<br><br><?php // je sais, ces <br> sont affreux, mais depuis le 'inline' des p et h2 ci-dessus, je n'ai pas mieux ! ?>

			<p class='cBraille'>
				(*) Si vous ne disposez pas encore d'identifiants, vous pouvez vous inscrire <a href='inscription.php'>ici.</a>
				(La CNIL protège vos données, cf mentions légales ci-dessous)
			</p>

			<p><?= ( ! empty($erreur) ) ? $erreur : "" ?></p>
			<form method='POST'>
				<div class='cChampForm'>
					<label for='iMail'>mail</label>
					<input type='text' id='iMail' name='mail' required autofocus placeholder='...'>
				</div>

				<div class='cChampForm'>
					<label for='idPassword'>mot de passe</label>
					<input type='password' id='idPassword' name='password' required placeholder='...'>
				</div>

				<div id='iValider'>
					<button class='cDecoBoutOK' name='valider'>Connexion</button>
				</div>
			</form>
		</section>

		<section id='iConnexionInscription'  class='cSectionContour'><h2>(*) Création d'un compte</h2>
			<p>Si vous ne disposez pas encore d'identifiants, vous pouvez vous inscrire en suivant le lien : </p>
			<p>(La CNIL protège vos données, cf mentions légales ci-dessous)</p>
			<p><a href='inscription.php'>>  inscription  <</a></p>
		</section>
	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>