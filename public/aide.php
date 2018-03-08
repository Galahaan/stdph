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

    <!-- Mots clés de la page -->
    <meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= MC_CP ?>, <?= MC_1 ?>, <?= MC_2 ?>, <?= $pageCourante['nom'] ?>'>
    <!-- Prise en compte du responsive design -->
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel='stylesheet' type='text/css' href='css/style.css'>
    <link rel='shortcut icon' href='img/favicon.ico'>
</head>
<body>
    <section id='iAide' class='cSectionContour'><h3>Aide à la navigation dans le site</h3>

        <h4>Raccourcis clavier</h4>
        <p>Quand ils existent, ils sont indiqués par des [lettres entre crochets].
        La combinaison de touches dépend de l'OS (Windows, Linux, Mac, ...) et du navigateur utilisé (Mozilla Firefox, Google Chrome, Microsoft Internet Explorer, Opera, Apple Safari, ...).
        <br>
        <a href='https://www.w3schools.com/tags/att_global_accesskey.asp'>source W3schools sur les raccourcis clavier</a>
        <br><br>
        <?= $_SERVER['HTTP_USER_AGENT'] ?>
        <br><br>
        <?php $combinaisonRC = combinaisonRC($_SERVER['HTTP_USER_AGENT']); $phrase = $combinaisonRC['phrase']; $combi = $combinaisonRC['combi']; ?>
        <?= $phrase ?>
        <br><br>
        exemple: [r] accueil => indique le raccourci clavier <?= $combi ?> + r pour retourner à l'<a href='index.php' accesskey='r'>accueil</a>
        </p>

        <h4>Bandeau supérieur</h4>
        <p>Toutes les pages du site contiennent, dans leur partie supérieure, un bandeau divisé en 4 parties :
            <ol>
                <li>un lien vers la page actuelle d'aide à la navigation</li>
                <li>un bandeau décoratif contenant un lien vers la page d'accueil (le lien englobe le logo croix + le nom de la pharmacie + son emplacement)</li>
                <li>un menu de navigation comprenant 4 entrées : page d'accueil, horaires, équipe et contact</li>
                <li>un lien de connexion / déconnexion, servant aux utilisateurs inscrits sur le site, pour accéder à certains services</li>
            </ol>
        </p>
        <p>Le contenu propre à chaque page se situe donc en dessous de ce bandeau.</p>

        <h4>Services proposés en page d'accueil</h4>
        <p>L'accès aux services proposés par la pharmacie se fait par la page d'accueil. En voici la liste :
            <ol>
                <li>préparation d'ordonnance (nécessite une inscription) : permet d'envoyer une ordonnance pour venir chercher les produits dès qu'ils auront été préparés (<a href='connexion.php'>lien direct</a>)</li>

                <li>préparation de commande (nécessite une inscription) : comme pour l'ordonnance, mais pour des produits sans prescription médicale (<a href='connexion.php'>lien direct</a>)</li>

                <li>liste des pharmacies de garde via le site resogarde, ou indications pour se rendre au commissariat de police (<a href='pharmaDeGarde.php'>lien direct</a>)</li>

                <li>promotions en cours (<a href='promos.php'>lien direct</a>)</li>

                <li>liste des marques de produits suivies par la pharmacie et rapidement disponibles (<a href='gammesProduits.php'>lien direct</a>)</li>

                <li>informations / conseils sur différents sujets (<a href='infos.php'>lien direct</a>)</li>
            </ol>
        </p>
    </section>
</body>
</html>