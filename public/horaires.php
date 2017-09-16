<?php
	///////////////////////////////////////////////////////////////////////////////////////////////
	/////     INCLUDE sécurisé
	///////////////////////////////////////////////////////////////////////////////////////////////

	if( empty($page) ){
	$page = "functions"; // page à inclure : functions.php

	// NB: functions.php inclut 'constantes.php'

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
	// if( preg_match('admin', $page) ){
	if( strpos($page, 'admin') ){
		echo "Vous n'avez pas accès à ce répertoire";
	}
	else{
	    // On vérifie que la page est bien sur le serveur
	    if (file_exists("includes/" . $page) && $page != 'index.php') {
	    	include_once("./includes/".$page);
	    }
	    else{
	    	echo "Erreur Include : le fichier " . $page . " est introuvable.";
	    }
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	/////     FIN INCLUDE sécurisé
	///////////////////////////////////////////////////////////////////////////////////////////////

	integreCLR('constantes_CLRS');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Pharmacie Le Reste</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="shortcut icon" href="img/favicon.ico">
</head>

<body>
	<header>
		<section>
			<a href="index.php">
				<img src="img/croix_mauve.png" alt="">
				<h1>Pharmacie Le Reste
					<p>Nantes, quartier Saint-Joseph de Porterie</p>
				</h1>
			</a>
			<p id="telIndex"><span>>> </span><a href="tel:+33240251580">02 40 25 15 80</a><span> <<</span></p>
		</section>
		<nav class="navigation">
			<ul>
				<li><a href="index.php"   >Accueil </a></li>
				<li><a href="horaires.php">Horaires</a></li>
				<li><a href="equipe.html"  >Équipe  </a></li>
				<li><a href="contact.php"  >Contact </a></li>
			</ul>
		</nav>
	</header>

	<main>
		<?php
			$aujourdhui = dateFr();				// fonction qui génère une date de la forme : vendredi 2 juillet 2017
			$auj = substr($aujourdhui, 0, 3);	// on garde les 3 1ères lettres de la chaîne

			$heure  = heureActuelle('');		// heure au format "décimal"
			$heureH = heureActuelle('H');		// heure au format "horaire", ie non décimal !

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
		<p><?= $aujourdhui . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class=\"rose\">". $heureH . "</span>" ?></p>
		<p><?= pharmacieOuverte( $auj, $heure ) ?></p>
		<section class="horaires">

			<article class="semaine" <?= ($auj == 'lun') ? "id=\"aujourdhui\"" : "" ?>  >
				<div>lundi</div><div <?= ($matinOff) ? "class=\"off\"" : "" ?> >8h30</div><div <?= ($matinOff) ? "class=\"off\"" : "" ?> >12h30</div><div class="tiret">-</div><div <?= ($apremOff) ? "class=\"off\"" : "" ?> >14h</div><div <?= ($apremOff) ? "class=\"off\"" : "" ?> >19h30</div>

				<?php // comme cette dernière <div> est en position absolute, c'est pas grave si on laisse
				      // de la place dans l'éditeur après la <div> précédente : l'espace ne se verra pas en HTML ?>

				<div <?= ($auj == 'lun' && $dessinerTrait == true) ? "id=\"trait\"" : "class=\"effacerTrait\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</div>

			</article>
			<article class="semaine" <?= ($auj == 'mar') ? "id=\"aujourdhui\"" : "" ?>  >
				<div>mardi</div><div <?= ($matinOff) ? "class=\"off\"" : "" ?> >8h30</div><div <?= ($matinOff) ? "class=\"off\"" : "" ?> >12h30</div><div class="tiret">-</div><div <?= ($apremOff) ? "class=\"off\"" : "" ?> >14h</div><div <?= ($apremOff) ? "class=\"off\"" : "" ?> >19h30</div>

				<div <?= ($auj == 'mar' && $dessinerTrait == true) ? "id=\"trait\"" : "class=\"effacerTrait\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</div>

			</article>
			<article class="semaine" <?= ($auj == 'mer') ? "id=\"aujourdhui\"" : "" ?>  >
				<div>mercredi</div><div <?= ($matinOff) ? "class=\"off\"" : "" ?> >8h30</div><div <?= ($matinOff) ? "class=\"off\"" : "" ?> >12h30</div><div class="tiret">-</div><div <?= ($apremOff) ? "class=\"off\"" : "" ?> >14h</div><div <?= ($apremOff) ? "class=\"off\"" : "" ?> >19h30</div>

				<div <?= ($auj == 'mer' && $dessinerTrait == true) ? "id=\"trait\"" : "class=\"effacerTrait\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</div>

			</article>
			<article class="semaine" <?= ($auj == 'jeu') ? "id=\"aujourdhui\"" : "" ?>  >
				<div>jeudi</div><div <?= ($matinOff) ? "class=\"off\"" : "" ?> >8h30</div><div <?= ($matinOff) ? "class=\"off\"" : "" ?> >12h30</div><div class="tiret">-</div><div <?= ($apremOff) ? "class=\"off\"" : "" ?> >14h</div><div <?= ($apremOff) ? "class=\"off\"" : "" ?> >19h30</div>

				<div <?= ($auj == 'jeu' && $dessinerTrait == true) ? "id=\"trait\"" : "class=\"effacerTrait\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</div>

			</article>
			<article class="semaine" <?= ($auj == 'ven') ? "id=\"aujourdhui\"" : "" ?>  >
				<div>vendredi</div><div <?= ($matinOff) ? "class=\"off\"" : "" ?> >8h30</div><div <?= ($matinOff) ? "class=\"off\"" : "" ?> >12h30</div><div class="tiret">-</div><div <?= ($apremOff) ? "class=\"off\"" : "" ?> >14h</div><div <?= ($apremOff) ? "class=\"off\"" : "" ?> >19h30</div>

				<div <?= ($auj == 'ven' && $dessinerTrait == true) ? "id=\"trait\"" : "class=\"effacerTrait\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</div>

			</article>
			<article class="samedi" <?= ($auj == 'sam') ? "id=\"aujourdhui\"" : "" ?>  >
				<div>samedi</div><div <?= ($samediOff) ? "class=\"off\"" : "" ?> >9h</div><div <?= ($samediOff) ? "class=\"off\"" : "" ?> >16h</div>

				<div <?= ($auj == 'sam' && $dessinerTrait == true) ? "id=\"trait\"" : "class=\"effacerTrait\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</div>

			</article>

			<p>Si vous venez en voiture, la pharmacie dispose d'un parking.</p>

			<p>En chronobus <span>C6</span>, vous descendez à l'arrêt <span>St Joseph de Porterie</span>,
				et il vous reste moins d'une minute à pied.</p>

			<p><b>En cas de garde</b>, la pharmacie reste ouverte jusqu'à <b>20h30</b>. Après 20h30, s'adresser au commissariat <b>Waldec-Rousseau</b>.</p>

		</section>
	</main>

	<footer>
		<section>
			<p>Pharmacie Le Reste</p>
			<p>21 rue du Bêle</p>
			<p>44300 Nantes</p>
			<p>tel - 02 40 25 15 80</p>
			<p>fax - 02 40 30 06 56</p>
		</section>
		<section>
			<p>Édition CLR - 2017</p>
		</section>
	</footer>
</body>
</html>