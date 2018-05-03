<?php

include('inclus/headerF.php');

// ici on est obligé d'utiliser la fonction native telle quelle, sinon elle ne peut pas jouer son rôle de "_once" :
require_once("./inclus/initDB.php");

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
	<main id='iMain'>
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

	<?php include('footer.php'); ?>

	<script src='scriptsJs/scripts.js' type='text/javascript'></script>

</body>
</html>