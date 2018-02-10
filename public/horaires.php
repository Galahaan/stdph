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
		<?php
			$aujourdhui = dateFr();				// fonction qui génère une date de la forme : vendredi 2 juillet 2017
			$auj = substr($aujourdhui, 0, 3);	// on garde les 3 1ères lettres de la chaîne

			$heure  = heureActuelle("");		// heure au format "décimal"
			$heureH = heureActuelle("H");		// heure au format "horaire", ie non décimal !

			// getDeltaP( $heure ) retourne la valeur en % dont il faut décaler (left: ) la div représentant le trait vertical
			// (en fonction de l'heure de la journée) mais également l'information s'il faut ou non afficher le trait.
			//
			// ATTENTION : ceci implique de ne RIEN changer aux valeurs de width et de padding left ou right du § CSS intitulé
			//							"largeurs et marges des jours et des créneaux horaires"

			$deltaP			= getDeltaP($heure)[0];
			$dessinerTrait	= getDeltaP($heure)[1];

			// passé 12h30, on "désactive" le créneau du matin, et passé 19h30, on "désactive" le créneau de l'après-midi,
			// pour le samedi, passé 16h, on désactive les 2 <div>,
			// ie qu'on les remet avec la couleur de fond, un peu plus pâle, des autres jours :
			$matinOff  = ( $heure >= FMATD ) ? true : false;
			$apremOff  = ( $heure >= FAMID ) ? true : false;
			$samediOff = ( $heure >= SA_FAMID ) ? true : false;

			// pharmacieOuverte() génère un message sur l'état d'ouverture ou de fermeture de la pharmacie (ou de leur proximité)
		?>
		<p><?= $aujourdhui . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class='cCouleurRose'>". $heureH . "</span>" ?></p>
		<p><?= pharmacieOuverte( $auj, $heure ) ?></p>
		<section class='cHoraires'><h3>Horaires d'ouverture de la <?= NOM_PHARMA ?></h3>

			<article class='cSemaine' <?= ($auj == "lun") ? "id='iAujourdhui'" : "" ?>  >
				<h3>lundi</h3><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >8h30</div><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >12h30</div><div class='cTiret'>-</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >14h</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >19h30</div>

				<?php // comme cette dernière <div> est en position absolute, c'est pas grave si on laisse
				      // de la place dans l'éditeur après la <div> précédente : l'espace ne se verra pas en HTML ?>

				<div <?= ($auj == "lun" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
			<article class='cSemaine' <?= ($auj == "mar") ? "id='iAujourdhui'" : "" ?>  >
				<h3>mardi</h3><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >8h30</div><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >12h30</div><div class='cTiret'>-</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >14h</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >19h30</div>

				<div <?= ($auj == "mar" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
			<article class='cSemaine' <?= ($auj == "mer") ? "id='iAujourdhui'" : "" ?>  >
				<h3>mercredi</h3><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >8h30</div><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >12h30</div><div class='cTiret'>-</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >14h</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >19h30</div>

				<div <?= ($auj == "mer" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
			<article class='cSemaine' <?= ($auj == "jeu") ? "id='iAujourdhui'" : "" ?>  >
				<h3>jeudi</h3><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >8h30</div><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >12h30</div><div class='cTiret'>-</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >14h</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >19h30</div>

				<div <?= ($auj == "jeu" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
			<article class='cSemaine' <?= ($auj == "ven") ? "id='iAujourdhui'" : "" ?>  >
				<h3>vendredi</h3><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >8h30</div><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >12h30</div><div class='cTiret'>-</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >14h</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >19h30</div>

				<div <?= ($auj == "ven" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
			<article class='cSamedi' <?= ($auj == "sam") ? "id='iAujourdhui'" : "" ?>  >
				<h3>samedi</h3><div <?= ($samediOff) ? "class='cCreneauOff'" : "" ?> >9h</div><div <?= ($samediOff) ? "class='cCreneauOff'" : "" ?> >16h</div>

				<div <?= ($auj == "sam" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
		</section>
		<section class='cHorairesInfos'><h3>Informations accès / horaires de garde de la <?= NOM_PHARMA ?></h3>
			<?php echo PARTICULARITES_PHARMACIE ?>
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