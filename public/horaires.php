<?php

	///////////////////////////////////////////////////////////////////////////////////////////////
	/////     INCLUDE sécurisé
	///////////////////////////////////////////////////////////////////////////////////////////////

	if( empty($page) ){
	$page = "functions"; // page à inclure : functions.php
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
	    	include("./includes/".$page);
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
			$aujourdhui = dateFr(); // fonction perso qui génère une date de la forme : vendredi 2 juillet 2017
			$auj = substr($aujourdhui, 0, 3); // on garde les 3 1ères lettres de la chaîne

			$heureH = heureActuelle('H');

			$heure = heureActuelle('');
			// 8h30 = référence 0 pour le trait vertical, même pour le samedi.
			// côté css, pour que le trait vertical soit à 8h30, il faut le décaler de la valeur du jour de la semaine, soit 23%.
			// et la journée de 8h30 à 19h30 se décompose ainsi :
			// 8h30  -> 12h30 : 4h   couverts par 14 + 14 = 28%  +  1 % de padding-left  +  1 % de padding-right  =  30 % en tout
			// 12h30 -> 14h   : 1h30 couverts par 5%
			// 14h   -> 19h30 : 5h30 couverts par 19 + 19 = 38%  +  1 % de padding-left  +  1 % de padding-right  =  40 % en tout

			if( $heure >= 8.5 && $heure < 19.5){

				$dessinerTrait = true; // pour afficher l'id 'trait' dans le div du jour

				// le nom 'deltaP' est sensé évoquer 'delta %'
				$deltaP = 23; // 23%, auxquels on va rajouter des % en fonction du créneau horaire

				if( $heure < 12.5 ){

					// les 4h de la matinée sont représentées par une width de 30% en CSS :
					$deltaP += ($heure - 8.5) / 4 * 30;

				}else if( $heure >= 12.5 && $heure < 14 ){

					// les 1h30 de la pause déjeuner (1.5 en décimal) sont représentées par une width de 5% :
					$deltaP += 30 + ($heure - 12.5) / 1.5 * 5;

				}else if( $heure >= 14 && $heure < 19.5 ){

					// les 5h30 de l'après-midi (5.5 en décimal) sont représentées par une width de 40% :
					$deltaP += 30 + 5 + ($heure - 14) / 5.5 * 40;
				}
			}
			else{
				$deltaP = 0; // en fait, on n'a pas besoin de deltaP dans ce cas, mais pour éviter un message d'erreur, on le met à 0
				$dessinerTrait = false; // pour afficher la classe 'effacerTrait' quand on est avant 8h30 ou après 19h30
			}

			// passé 12h30, on "désactive" le créneau du matin, et passé 19h30, on "désactive" le créneau de l'après-midi,
			// pour le samedi, passé 16h, on désactive les 2 <div>,
			// ie qu'on les remet avec la couleur de fond, un peu plus pâle, des autres jours :
			$matinOff  = ( $heure >= 12.5 ) ? true : false;
			$apremOff  = ( $heure >= 19.5 ) ? true : false;
			$samediOff = ( $heure >= 16   ) ? true : false;

			// pharmacieOuverte() génère un message sur l'état d'ouverture ou de fermeture de la pharmacie (ou de leur proximité)
		?>
		<p><?= $aujourdhui . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class=\"rose\">". $heureH . "</span>" ?></p>
		<p><?= pharmacieOuverte() ?></p>
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