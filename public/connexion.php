<?php

include('inclus/enteteP.php');

require_once("./inclus/initDB.php");

$erreur = "";

if( isset($_POST['connexion']) && !empty($_POST['mail']) ){

	if( mailValide($_POST['mail']) ){ $mail = $_POST['mail']; }
	else{ $mail = ''; }

	if( !empty($_POST['password']) ){

		if( mdpValide($_POST['password']) ){ $password = $_POST['password']; }
		else{ $password = ''; }


		// Petit aparté sur le fonctionnement du statut du mot de passe :
		// - par défaut, ie à la création d'un compte, le statut est à 'on'
		//   . qd le client se connecte, le statut n'est pas modifié et reste à 'on'
		//   . si le client modifie son mdp, le statut est mis à 'on', donc inchangé dans ce cas
		//
		// - si le client déclenche 'mdp oublié' => le mdp est réinitialisé et le statut passe à 'reset'
		//   . à la 1ère connexion, le statut est modifié et passe à 'off'
		//   . si le client modifie son mdp (comme il le lui est demandé), le statut est mis à 'on', donc on repart en fonctionnement nominal
		//   . si le client ne modifie pas son mdp, il ne pourra pas se reconnecter puisque le statut du mdp sera resté à 'off'
		//     (il devra à nouveau cliquer sur 'mdp oublié')


		// récupération des données du client, dont le mot de passe crypté :
		$phraseRequete = "SELECT * FROM " . TABLE_CLIENTS . " WHERE mail = '" . $mail . "'";
		$requete = $dbConnex->prepare($phraseRequete);
		$requete->execute();
		$client = $requete->fetch();

		if( ! empty($client) ){
			if( password_verify($password, $client['pwd']) && (($client['pwdStatus'] == 'on') || ($client['pwdStatus'] == 'reset')) ){

				// c'est le bon mot de passe, et son statut est valide, on ouvre donc la session
				$_SESSION['client']['civilite'] = $client['civilite'];
				$_SESSION['client']['nom'] = $client['nom'];
				$_SESSION['client']['prenom'] = $client['prenom'];
				$_SESSION['client']['mail'] = $client['mail'];
				$_SESSION['client']['tel'] = $client['tel'];

				// si c'est la 1ère connexion après un reset du mdp, on le 'désactive'
				if( $client['pwdStatus'] == 'reset' ){
					$phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET pwdStatus='off' WHERE id=" . $client['id'];
					$requete = $dbConnex->prepare($phraseRequete);
					$requete->execute();
				}

				// juste avant de retourner à l'accueil, on stocke en BDD la date de cette connexion
				// => c'est la 1ère étape pour respecter la déclaration à la CNIL sur la durée de stockage des données
				//    (la 2e étape consistera à détruire ces données quand elles auront dateConx + 1 an)
				$erreurRequete = false;
				$phraseRequete = "UPDATE ". TABLE_CLIENTS . " SET dateConx= '" . date('Y-m-d H-i-s') .  "' WHERE id= " . $client['id'];
				$requete = $dbConnex->prepare($phraseRequete);
				if( $requete->execute() != true ){ $erreurRequete = true; }
				//pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...

				// et en plus, si jamais le client avait, lors d'une session précédente, demandé un code
				// d'authentification pour modifier ses données (mon-compte), sans s'en être servi => on initialise
				// les variables de SESSION concernées, et on réinitialise aussi les valeurs restées intactes en BDD
				$_SESSION['client']['nbEssaisCodeRestants'] = 0;
				$_SESSION['client']['codeDateV']            = 0;
				$_SESSION['client']['mAutor']               = false;
				$phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='&#&##&#&', codeDateV='0' WHERE id =" . $client['id'];
				$requete = $dbConnex->prepare($phraseRequete);
				if( $requete->execute() != true ){ $erreurRequete = true; }
				//pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...

				// enfin, on retourne à l'accueil :
				// A noter : ici, la fonction header fonctionne bien parce qu'on est bien au dessus
				//           du DOCTYPE et que la page HTML n'a pas encore commencé à être chargée.
				header('Location: index.php');
			}
			else{
				$erreur = "Mot de passe invalide ...";
			}
		}
		else{
			$erreur = "Identifiant inconnu ...";
		}
	}
	// else : "il faut renseigner un MdP ..."
}
// else : "il faut renseigner un mail ..."

include('inclus/enteteH.php');
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
					<input type='text' id='iMail' name='mail' required placeholder='...' autofocus value='<?= isset($mail) ? $mail : "" ?>' >
				</div>

				<div class='cChampForm'>
					<label for='idPassword'>mot de passe</label>
					<input type='password' id='idPassword' name='password' required placeholder='...'>
				</div>

				<div id='iValider'>
					<button class='cDecoBoutonValid' name='connexion'>Connexion</button>
					<a href='reinitMdp.php<?= !empty($mail) ? "?mail=".$mail : "" ?>' class='cDecoBoutonAutre' >mot de passe oublié</a>
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