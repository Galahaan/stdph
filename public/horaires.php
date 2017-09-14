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

				$maintenantOK = true; // pour afficher l'id 'maintenant' dans le aside du jour

				$deltaP = 23; // 23%, auxquels on va rajouter des % en fonction du créneau horaire

				if( $heure < 12.5 ){

					$deltaP += ($heure - 8.5) / 4 * 30; // 30% couvrent 4h

				}else if( $heure >= 12.5 && $heure < 14 ){

					$deltaP += 30 + ($heure - 12.5) / 1.5 * 5; // 5% couvrent 1h30 soit 1.5 en décimal

				}else if( $heure >= 14 && $heure < 19.5 ){

					$deltaP += 30 + 5 + ($heure - 14) / 5.5 * 40; // 40% couvrent 5h30 soit 5.5 en décimal
				}
			}
			else{
				$maintenantOK = false; // pour afficher la classe 'effacerAside' quand on est avant 8h30 ou après 19h30
			}

			// pharmacieOuverte() génère un message sur l'état d'ouverture ou de fermeture de la pharmacie (ou de leur proximité)
		?>
		<p><?= $aujourdhui . " - ". $heureH ?></p>
		<p><?= pharmacieOuverte() ?></p>
		<section class="horaires">

			<article class="semaine" <?= ($auj == 'lun') ? "id=\"aujourdhui\"" : "" ?>>
				<aside>lundi</aside><aside>8h30</aside><aside>12h30</aside><aside>-</aside><aside>14h</aside><aside>19h30</aside><aside <?= ($auj == 'lun' && $maintenantOK == true) ? "id=\"maintenant\"" : "class=\"effacerAside\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</aside>
			</article>
			<article class="semaine" <?= ($auj == 'mar') ? "id=\"aujourdhui\"" : "" ?>>
				<aside>mardi</aside><aside>8h30</aside><aside>12h30</aside><aside>-</aside><aside>14h</aside><aside>19h30</aside><aside <?= ($auj == 'mar' && $maintenantOK == true) ? "id=\"maintenant\"" : "class=\"effacerAside\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</aside>
			</article>
			<article class="semaine" <?= ($auj == 'mer') ? "id=\"aujourdhui\"" : "" ?>>
				<aside>mercredi</aside><aside>8h30</aside><aside>12h30</aside><aside>-</aside><aside>14h</aside><aside>19h30</aside><aside <?= ($auj == 'mer' && $maintenantOK == true) ? "id=\"maintenant\"" : "class=\"effacerAside\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</aside>
			</article>
			<article class="semaine" <?= ($auj == 'jeu') ? "id=\"aujourdhui\"" : "" ?>>
				<aside>jeudi</aside><aside>8h30</aside><aside>12h30</aside><aside>-</aside><aside>14h</aside><aside>19h30</aside><aside <?= ($auj == 'jeu' && $maintenantOK == true) ? "id=\"maintenant\"" : "class=\"effacerAside\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</aside>
			</article>
			<article class="semaine" <?= ($auj == 'ven') ? "id=\"aujourdhui\"" : "" ?>>
				<aside>vendredi</aside><aside>8h30</aside><aside>12h30</aside><aside>-</aside><aside>14h</aside><aside>19h30</aside><aside <?= ($auj == 'ven' && $maintenantOK == true) ? "id=\"maintenant\"" : "class=\"effacerAside\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</aside>
			</article>
			<article id="samedi" <?= ($auj == 'sam') ? "id=\"aujourdhui\"" : "" ?>>
				<aside>samedi</aside><aside>&nbsp;</aside><aside>9h</aside><aside>16h</aside><aside <?= ($auj == 'sam' && $maintenantOK == true) ? "id=\"maintenant\"" : "class=\"effacerAside\"" ?> style="left:<?= $deltaP ?>%">&nbsp;</aside>
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