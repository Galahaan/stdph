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

	// on détermine la page courante, en vue de souligner le lien
	// concerné dans le menu de navigation grâce à l'id 'iPageCourante' :
	$flagPC = pageCourante($_SERVER['REQUEST_URI']);

?>

<!DOCTYPE html>
<html lang='fr'>
<head>
	<title><?= NOM_PHARMA ?></title>
	<meta charset='utf-8'>

	<!-- Mots clés de la page -->
	<meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= MC_CP ?>, <?= MC_1 ?>, <?= MC_2 ?>'>

	<!-- Prise en compte du responsive design -->
	<meta name='viewport' content='width=device-width, initial-scale=1'>

	<!-- intégrer le CDN de fontAwesome -->
	<!-- on le place AVANT l'appel à notre CSS pour se donner la possibilité -->
	<!-- de le modifier dans notre CSS puisque le fichier HTML est lu de haut en bas -->
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
				<li><a <?= ($flagPC == "1000") ? "id = 'iPageCourante'" : "" ?> href='index.php'   >Accueil </a></li>
				<li><a <?= ($flagPC == "0100") ? "id = 'iPageCourante'" : "" ?> href='horaires.php'>Horaires</a></li>
				<li><a <?= ($flagPC == "0010") ? "id = 'iPageCourante'" : "" ?> href='equipe.php'  >Équipe  </a></li>
				<li><a <?= ($flagPC == "0001") ? "id = 'iPageCourante'" : "" ?> href='contact.php' >Contact </a></li>
			</ul>
		</nav>

		<div class='cBandeauConnex'>
			<?php
				if( isset($_SESSION['client']) ){

					// si le client est connecté, on affiche son nom et le lien pour se déconnecter :
					echo "<div class='cClientConnecte'>";
						echo $_SESSION['client']['prenom'] . ' ' . $_SESSION['client']['nom'];
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
		<section class='cIntro'><h3>Etat actuel d'ouverture de la <?= NOM_PHARMA ?></h3>
			<?php
				$aujourdhui = dateFr();				// fonction qui génère une date de la forme : vendredi 2 juillet 2017
				$auj = substr($aujourdhui, 0, 3);	// on garde les 3 1ères lettres de la chaîne (en vue de l'appel de 'pharmacieOuverte')
				$heure = heureActuelle('');			// on demande l'heure au format décimal
			?>
			<p><?= pharmacieOuverte( $auj, $heure ) ?></p>

		</section>
		<section class='cVignettes'><h3>Services proposés par la <?= NOM_PHARMA ?></h3>
			
			<nav class='cBraille'>
				<ul>
					<li><a href='#iPrepaOrdonnance'>Préparation d'ordonnance</a></li>
					<li><a href='#iPrepaCommande'>Préparation de commande</a></li>
					<li><a href='#iGammesProduits'>Les gammes de produits</a></li>
					<li><a href='#iPharmaDeGarde'>Pharmacies de garde</a></li>
					<li><a href='#iPromos'>Promotions</a></li>
					<li><a href='#iInfos'>Informations / Conseils</a></li>
					<li><a href='#iHumour'>Humour</a></li>
				</ul>
			</nav>


			<article id='iPrepaOrdonnance'>
				<?php if(! empty($_SESSION)) : ?>
					<a href='prepaOrdonnance.php'>
						<h4>Préparation d'ordonnance</h4>
					</a>
				<?php else : ?>
					<a href='connexion.php'>
						<h4>Préparation d'ordonnance</h4>
					</a>
				<?php endif ?>
				<img src='img/prepaOrdonnance.jpg' alt=''>
			</article>

			<article id='iPrepaCommande'>
				<?php if(! empty($_SESSION)) : ?>
					<a href='prepaCommande.php'>
						<h4>Préparation de commande</h4>
					</a>
				<?php else : ?>
					<a href='connexion.php'>
						<h4>Préparation de commande</h4>
					</a>
				<?php endif ?>
				<img src='img/prepaCommande.jpg' alt=''>
			</article>

			<article id='iGammesProduits'>
				<a href='gammesProduits.php'>
					<h4>Les gammes de produits</h4>
				</a>
				<img src='img/gammesProduits.jpg' alt=''>
			</article>

			<article id='iPharmaDeGarde'>
				<a href='pharmaDeGarde.php'>
					<h4>Pharmacies de garde</h4>
				</a>
				<img src='img/pharmaDeGarde.jpg' alt=''>
			</article>

			<article id='iPromos'>
				<a href='promos.php'>
					<h4>Promos</h4>
				</a>
				<img src='img/promos.jpg' alt=''>
			</article>

			<article id='iInfos'>
				<a href='infos.php'>
					<h4>Informations / Conseils</h4>
				</a>
				<img src='img/questions.jpg' alt=''>
			</article>

			<?php
				// matériel médical / contention ?

				// <article id='iHumour'>
				// 	<a href='humour.php'>
				// 		<h4>La blague de Chuck Norris !..</h4>
				// 	</a>
				// 	<img src='img/humour.jpg' alt=''>
				// </article>
			?>
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