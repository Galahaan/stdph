<?php

session_start(); // en début de chaque fichier utilisant $_SESSION

//////////////////////////////////     § anti-aspiration du site     /////////////////////////////////

if( isset($_SESSION['isAspi']) ){

    // on s'envoie un mail contenant l'adresse IP du visiteur
    // (car il s'agit peut être d'un vrai moteur de recherche)

    // 1) adresse IP du visiteur
    if( isset($_SERVER['HTTP_CLIENT_IP']) ){
        $ip = $_SERVER['HTTP_CLIENT_IP']; // IP si internet partagé
    }
    elseif( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; // IP derrière un proxy
    }
    else {
        $ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''); // IP 'normale'
    }


    $domaine = @gethostbyaddr($ip) or ($domaine = 'IP non résolue');

    $contenu = '<html><head><title>Aspirateur</title></head><body>'.
    'Aspirateur détecté ... confirmation ?<br><br>'.
    'Son IP : '.$ip.'<br>'.
    'Domaine : '.$domaine.''.
    '</body></html>';

    mail("clr.tstph@use.startmail.com", "Aspirateur ?..", $contenu, "From: bigouigfiy@cluster020.hosting.ovh.net\r\nReply-To: \r\nContent-Type: text/html; charset=\"iso-8859-1\"\r\n");

    // on bloque l'affichage
    echo "Aspirer un site, c'est mal ...";
    exit();
}
//////////////////////////////////////////////////////////////////////////////////////////////////////





ini_set("display_errors", 1);  // affichage des erreurs - à virer à la mise en prod !

///////////////////////////////////////////////////////////////////////////////////////////////
/////     INCLUDE sécurisé
///////////////////////////////////////////////////////////////////////////////////////////////

if( empty($page) ){
$page = "fonctions"; // page à inclure : fonctions.php qui lui-même inclut constantes.php

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
    // (à propos des chemins, même si header.php est colatéral à fonctions.php,
    //  comme il est inclus à partir d'un fichier situé un cran au-dessus, c'est
    //  cette première position qui compte)
    if (file_exists("inclus/".$page) && $page != "index.php") {
        require_once("./inclus/".$page);
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

    <link rel='stylesheet' type='text/css' href='css/styleCouleurs.css'>
    <link rel='stylesheet' type='text/css' href='css/style.css'>
    <link rel='shortcut icon' href='img/icones/favicon.ico'>
</head>

<body onload='placerFocus("iFocus")'>
    <header>
        <div id='iPiegeAR'><a href='stopRobots.php'><img src='pixel.png'></a></div>
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
                <img id='iLogoCroix' src='img/bandeau/croix_caducee.png' alt=''>
                <h1><?= NOM_PHARMA ?></h1>
                <h2><?= STI_PHARMA ?></h2>
            </a>
            <p id='iTelBandeau'><a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a><img class='cClicIndexTaille' src='img/icones/clicIndex.png' alt=''></p>
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
                    // (normalement, pour prepaOrdo et prepaCom, on ne peut arriver là sans être déjà connecté)
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
