<?php

session_start(); // en début de chaque fichier utilisant $_SESSION
ini_set("display_errors", 1);  // affichage des erreurs - à virer à la mise en prod !

///////////////////////////////////////////////////////////////////////////////////////////////
/////     INCLUDE sécurisé
///////////////////////////////////////////////////////////////////////////////////////////////

if( empty($page) ){
$page = "functions"; // page à inclure : functions.php qui lui-même inclut constantes.php

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
    if (file_exists("include/" . $page) && $page != "index.php") {
    	require_once("./include/".$page);
    }
    else{
    	echo "Erreur Include : le fichier " . $page . " est introuvable.";
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////
/////     FIN INCLUDE sécurisé
///////////////////////////////////////////////////////////////////////////////////////////////

?>
<!DOCTYPE html>
<html lang='fr'>
<head>
	<title><?= NOM_PHARMA ?></title>
	<meta charset='utf-8'>
	<meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= MC_CP ?>, <?= MC_1 ?>, <?= MC_2 ?>'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' rel='stylesheet' integrity='sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1' crossorigin='anonymous'>
	<link rel='stylesheet' type='text/css' href='css/style.css'>
	<link rel='shortcut icon' href='img/favicon.ico'>
</head>

<body>
	<header>
		<section>
			<a href='index.php'>
				<img id='iLogoCroix' src='img/croix_caducee.png' alt=''>
				<h1><?= NOM_PHARMA ?></h1>
				<h2><?= STI_PHARMA ?></h2>
			</a>
			<p id='iTelBandeau'><a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a><img class='cClicIndexTaille' src='img/clicIndex.png' alt=''></p>
		</section>
		<nav class='cNavigation'>
			<ul>
				<li><a href='index.php'   >Accueil </a></li>
				<li><a href='horaires.php'>Horaires</a></li>
				<li><a href='equipe.php'  >Équipe  </a></li>
				<li><a href='contact.php' >Contact </a></li>
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
		<?php $heure = heureActuelle('d'); ?>

		<section id='iPdG3237' class='cSectionContour'><h3>Trouver la pharmacie de garde</h3>

		<?php // si les gardes fonctionnent sans passer par le commissariat, ou si on est dans la journée : ?>
		<?php if( (HEURE_SOIR_POLICE_D == "X") || ((HEURE_MATIN_POLICE_D <= $heure) && ($heure < HEURE_SOIR_POLICE_D)) ) : ?>
			<p>Trouvez la <span>pharmacie de garde</span> la plus proche de chez vous en cliquant sur la croix ci-dessous :</p>
			<p id='i3237On'>
				<a href='http://www.3237.fr/'>
					<img src='img/croix_garde.png' alt=''>
					<!-- <span class='cBraille'>croix</span> -->
				</a>
			</p>
		<?php else : // on est en horaires de garde -> on affiche juste le titre ?>
			<p id='i3237Off'><span>Pharmacie de garde</span></p>
		<?php endif ?>

		</section>

		<section id='iPdGplan' class='cSectionContour'><h3>Localiser le commissariat de police</h3>

		<?php // si les gardes fonctionnent sans passer par le commissariat, il n'y a RIEN d'autre à afficher, d'où le 1e test : ?>
		<?php if( HEURE_SOIR_POLICE_D != "X" ) : ?>
			<?php // quelle que soit l'heure, on informe les gens du fonctionnement en horaires de garde, et on propose le plan : ?>
			<p>À partir de <span><?php echo HEURE_SOIR_POLICE_H ?></span>, et jusqu'à <span><?php echo HEURE_MATIN_POLICE_H ?></span> le lendemain matin, il faut se rendre, avec une pièce d'<span>identité</span> et une <span>ordonnance</span>, au <span>commissariat de police</span> situé :</p>
			<p><?php echo ADRESSE_POLICE ?></p>
			<p>Si vous utilisez un smartphone, profitez de son GPS pour vous y rendre :</p>
			<p>- cliquez sur le plan ci-dessous</p>
			<p>- puis sur l'icône &nbsp;<img src='img/itineraire.png' alt='itinéraire'></p>
			<p>... et laissez-vous guider.</p>
			<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2709.4418958453143!2d-1.5537605841504296!3d47.227501579161355!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4805ee99ed6c5d25%3A0x18995709d53782b2!2sCommissariat+de+Police+Central+de+Nantes!5e0!3m2!1sfr!2sfr!4v1517266865893" allowfullscreen></iframe>
		<?php endif ?>

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