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

// on détermine la page courante ...
// 1° => pour souligner le mot dans le menu de nav. : $pageCourante['flag']
// 2° => pour compléter le 'title' et le menu destinés à l'accessibilité : $pageCourante['nom']
$pageCourante = pageCourante($_SERVER['REQUEST_URI']);

?>
<!DOCTYPE html>
<html lang='fr'>
<head>
	<title><?= NOM_PHARMA . " - " . $pageCourante['nom'] ?></title>
	<meta charset='utf-8'>
	<meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= MC_CP ?>, <?= MC_1 ?>, <?= MC_2 ?>, <?= $pageCourante['nom'] ?>'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' rel='stylesheet' integrity='sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1' crossorigin='anonymous'>
	<link rel='stylesheet' type='text/css' href='css/style.css'>
	<link rel='shortcut icon' href='img/favicon.ico'>
</head>

<body>
	<header>
		<nav class='cBraille'><?= $pageCourante['nom'] ?>
			<ol>
				<li><a href='aide.php'     accesskey='h'>[h] Aide à la navigation dans le site</a></li>
				<li><a href='#iNavigation' accesskey='n'>[n] Menu de navigation</a></li>
				<li><a href='#iLienConnex' accesskey='c'>[c] Connexion/Inscription/Deconnexion</a></li>
				<li><a href='#iMain'       accesskey='m'>[m] contenu de <?= $pageCourante['nom'] ?></a></li>
			</ol>
		</nav>

		<section>
			<a href='index.php' accesskey='r'>
				<img id='iLogoCroix' src='img/croix_caducee.png' alt=''>
				<h1><?= NOM_PHARMA ?></h1>
				<h2><?= STI_PHARMA ?></h2>
			</a>
			<p id='iTelBandeau'><a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a><img class='cClicIndexTaille' src='img/clicIndex.png' alt=''></p>
		</section>
		<nav id='iNavigation'>
			<ul>
				<li><a <?= ($pageCourante['flag'] == "1000") ? "id = 'iPageCourante'" : "" ?> href='index.php'   >Accueil </a></li>
				<li><a <?= ($pageCourante['flag'] == "0100") ? "id = 'iPageCourante'" : "" ?> href='horaires.php'>Horaires</a></li>
				<li><a <?= ($pageCourante['flag'] == "0010") ? "id = 'iPageCourante'" : "" ?> href='equipe.php'  >Équipe  </a></li>
				<li><a <?= ($pageCourante['flag'] == "0001") ? "id = 'iPageCourante'" : "" ?> href='contact.php' >Contact </a></li>
			</ul>
		</nav>
		<div id='iBandeauConnex'>
			<?php
				if( isset($_SESSION['client']) ){

					// si le client est connecté, on affiche son nom et le lien pour se déconnecter :
					echo "<div id='iClientConnecte'>";
						echo $_SESSION['client']['prenom'] . " " . $_SESSION['client']['nom'];
					echo "</div>";

					echo "<div id='iLienConnex'>";
						echo "<a href='deconnexion.php'>déconnexion</a>";
					echo "</div>";
				}
				else{

					// si le client n'est pas connecté, on affiche le lien pour se connecter :
					echo "<div id='iClientConnecte'>";
						echo " ";
					echo "</div>";

					echo "<div id='iLienConnex'>";
						echo "<a href='connexion.php'>connexion</a>";
					echo "</div>";
				}
			?>
		</div>
	</header>

	<main id='iMain'>
		<img src='img/equipe.jpg' alt='Valérie, pharmacien titulaire, Hélène, pharmacien assistante, Christine, employée, et Cécile, apprentie'>
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