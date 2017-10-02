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

	<!-- Mots clés de la page -->
	<meta name="keywords" content="pharmacie, le reste, saint-joseph-de-porterie, joseph, porterie">

	<!-- Prise en compte du responsive design -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- intégrer le CDN de fontAwesome -->
	<!-- on le place AVANT l'appel à notre CSS pour se donner la possibilité -->
	<!-- de le modifier dans notre CSS puisque le fichier HTML est lu de haut en bas -->
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">

	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="shortcut icon" href="img/favicon.ico">
</head>

<body>
	<header>
		<section>
			<a href="index.php">
				<img src="img/croix_mauve.png" alt="">
				<h1>Pharmacie Le Reste</h1>
				<h2>Nantes, quartier Saint-Joseph de Porterie</h2>
			</a>
			<p id="telIndex"><i class="fa fa-volume-control-phone" aria-hidden="true"></i>&nbsp;&nbsp;<a href="tel:+33240251580">02 40 25 15 80</a></p>
		</section>
		<nav class="navigation">
			<ul>
				<li><a href="index.php"   >Accueil </a></li>
				<li><a href="horaires.php">Horaires</a></li>
				<li><a href="equipe.html" >Équipe  </a></li>
				<li><a href="contact.php" >Contact </a></li>
			</ul>
		</nav>
	</header>

	<main>
		<section class="intro"><h3>Etat actuel d'ouverture de la pharmacie Le Reste</h3>
			<?php
				$aujourdhui = dateFr();				// fonction qui génère une date de la forme : vendredi 2 juillet 2017
				$auj = substr($aujourdhui, 0, 3);	// on garde les 3 1ères lettres de la chaîne
				$heure = heureActuelle('');			// on demande l'heure au format décimal
			?>
			<p><?= pharmacieOuverte( $auj, $heure ) ?></p>

		</section>
		<section class="vignettes"><h3>Services proposés par la pharmacie Le Reste</h3>
			<article>
				<a href="prepaOrdonnance.php">
					<h4>Préparation d'ordonnance</h4>
				</a>
				<img src="img/prepaOrdonnance.jpg" alt="">
			</article>
			<article>
				<a href="prepaCommande.php">
					<h4>Préparation de commande</h4>
				</a>
				<img src="img/prepaCommande2.png" alt="">
			</article>
			<article>
				<a href="gammesProduits.html">
					<h4>Les gammes de produits</h4>
				</a>
				<img src="img/gammesProduits.jpg" alt="">
			</article>
			<article>
				<a href="pharmaDeGarde.php">
					<h4>Pharmacies de garde</h4>
				</a>
				<img src="img/pharmaDeGarde2.jpg" alt="">
			</article>
			<article>
				<a href="promos.html">
					<h4>Promos et cadeaux</h4>
				</a>
				<img src="img/promos1.jpg" alt="">
			</article>
			<article>
				<a href="infos.html">
					<h4>Informations / Conseils</h4>
				</a>
				<img src="img/questions1.jpg" alt="">
			</article>
	<!-- matériel médical / contention ? /   -->
			<article>
				<a href="humour.php">
					<h4>La blague de Chuck Norris !..</h4>
				</a>
				<img src="img/humour2.jpg" alt="">
			</article>
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