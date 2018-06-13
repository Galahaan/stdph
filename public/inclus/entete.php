<?php

session_start(); // en début de chaque fichier utilisant $_SESSION

ini_set("display_errors", 1);  // affichage des erreurs - à virer à la mise en prod !


//////////////////////////////////////////////////////////////////////////////////////////////////////
/////     § anti-aspiration du site
//////////////////////////////////////////////////////////////////////////////////////////////////////

// si $_SESSION['bot'] est définie, c'est que l'on revient de tapette.php,
// ie que la tapette s'est déclenchée, on veut donc en savoir plus !
// => on s'envoie un mail avec les IP et les noms de domaine du robot

if( isset($_SESSION['bot']) ){

    // histoire de n'envoyer le mail qu'une seule fois, si jamais le robot insiste :
//    if( !isset($_SESSION['bot']['mailEnvoye']) ){

        $_SESSION['bot']['mailEnvoye'] = true;

        $contenu =  "<html>" .
                    "<body>" .
                        "<b>" . date('D j M Y') . " - " . date('G\hi') . " - Moteur de recherche ou Aspirateur ?" . "</b>" .
                        "<br><br>" .
                        "IP 1      : " . $_SESSION['bot']['ip1'] . "<br>" .
                        "Domaine 1 : " . $_SESSION['bot']['do1'] . "<br>";

        if( $_SESSION['bot']['isAspi'] == true ){
            // si on est dans ce cas, c'est que les 2 IP sont différentes, donc on complète le contenu :
            $contenu .=
                        "<br>" .
                        "IP 2      : " . $_SESSION['bot']['ip2'] . "<br>" .
                        "Domaine 2 : " . $_SESSION['bot']['do2'] . "<br>" .
                        "<br>Ce robot a donc été bloqué ..." . "<br><br>";
        }

        // message de fin et balises de clôture du contenu :
        $contenu .=
                        "<br>" .
                        "Plus d'infos peut-être sur " .
                        "<a href='http://www.user-agents.org/'>user-agents.org</a>" .
                    "</body>" .
                    "</html>";

        // Envoi du mail :
        mail( $_SESSION['bot']['mailDest'],
                date('d/m/y') . " - " . date('G\hi') . " - passage de " . $_SESSION['bot']['do1'],
                $contenu,
                "From: " . $_SESSION['bot']['url'] . " <" . $_SESSION['bot']['mailExp'] . ">" .
                "\r\nReply-To: \r\nContent-Type: text/html; charset=\"UTF-8\"\r\n"
            );
//    }
}

if( $_SESSION['bot']['isAspi'] == true ){

    // on bloque l'affichage après un petit message
    echo "rather see elsewhere, please";
    exit();
}

//////////////////////////////////////////////////////////////////////////////////////////////////////
/////     INCLUDE sécurisé
//////////////////////////////////////////////////////////////////////////////////////////////////////

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
//////////////////////////////////////////////////////////////////////////////////////////////////////

// on détermine la page courante ...
// 1° => pour souligner le mot dans le menu de nav. : $pageCourante['flag']
// 2° => pour compléter le 'title' et le menu destinés à l'accessibilité : $pageCourante['nom']
$pageCourante = pageCourante($_SERVER['REQUEST_URI']);

// Quand on arrive directement sur l'URL du site, ie sans preciser '/index.php',
// $pageCourante n'est alors pas définie, ce qui génère un <title> incomplet => donc on le complète !
if( !isset($pageCourante['nom']) ){ $pageCourante['nom'] = PAGE_ACCUEIL; $pageCourante['flag'] = "1000"; }

// pour personnaliser l'entete en fonction de la page qui l'a appelé
// (appel d'un CDN, refresh de la page, positionnement d'un focus, ...)
$enteteSpecs = enteteSpecs($_SERVER['REQUEST_URI']);

?>
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>

    <?php // Cette balise sert à dire à Google : "je suis le propriétaire du site, et je souhaite utiliser,        ?>
    <?php // par l'intermédiaire de mon compte Google, les outils de suivi de référencement proposés par Google."  ?>
    <?php // Cette balise DOIT être conservée de façon permanente tant que l'on souhaite utiliser ces outils.      ?>
    <meta name="google-site-verification" content="<?= GOOGLE_VALIDATION_CODE ?>" />

    <?php // Pour un bon positionnement dans les résultats des moteurs de recherche, renseigner     ?>
    <?php // ces balises est très important, surtout title (max 65 c.) et description (max 200 c.)  ?>
    <title><?= $pageCourante['nom'] . ", " . NOM_PHARMA . LOC_PHARMA_TTL ?></title>
    <meta name='description' content='<?= $enteteSpecs['description'] ?>'>
    <meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= $pageCourante['nom'] ?>, <?= MC_1 ?>, <?= MC_2 ?>, <?= MC_3 ?>'>
    <?= ! empty($enteteSpecs['robots']) ? "<meta name='robots' content='" . $enteteSpecs['robots'] . "'>" : "" ?>

    <?php // Prise en compte du responsive design ?>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <?php
    // selon les pages, on peut vouloir un refresh automatique (notamment quand l'heure est affichée !)
    // (mais pas pour Lynx, qui ne le prend pas en compte, et ça gène la navigation)                      ?>
    <?php if( strpos($_SERVER['HTTP_USER_AGENT'], 'ynx') == FALSE ) : ?>
    <?= ! empty($enteteSpecs['refresh']) ? "<" . $enteteSpecs['refresh'] : "" ?>
    <?php endif ?>

    <?php // selon les pages, on peut avoir besoin du CDN de fontAwesome.
          // (on l'appelle en PREMIER, comme ça notre CSS reste prioritaire puisque le fichier HTML est lu de haut en bas) ?>
    <?= ! empty($enteteSpecs['cdn'])     ? "<" . $enteteSpecs['cdn']     : "" ?>

    <link rel='stylesheet' type='text/css' href='css/theme.css'>
    <link rel='stylesheet' type='text/css' href='css/style.css'>
    <link rel='shortcut icon' href='img/icones/favicon.ico'>
</head>

<body <?= $enteteSpecs['focus'] ?> >
    <header>

        <?php // pour Lynx, on enlève le lien vers le piège.                             ?>
        <?php // il n'est pas grave en soi, mais ça fait un lien inutile en haut de page ?>
        <?php if( strpos($_SERVER['HTTP_USER_AGENT'], 'ynx') == FALSE ) : ?>
        <div id='iPiegeAA'><a href='tapette.php'><img src='img/bandeau/tapette.png' alt='tapette à moucherons'></a></div>
        <?php endif ?>

        <nav class='cBraille'><?= $pageCourante['nom'] ?>
            <ol>
                <li><a href='#iMain'       accesskey='c'>[c] contenu de la page <?= $pageCourante['nom'] ?></a></li>
                <li><a href='#iNavigation' accesskey='n'>[n] Menu de navigation</a></li>
                <li><a href='#iLienConnex' accesskey='x'>[x] Connexion/Inscription/Deconnexion</a></li>
                <li><a href='aide.php'     accesskey='h'>[h] Aide à la navigation dans le site</a></li>
            </ol>
        </nav>

        <section>
            <a href='index.php' accesskey='r'>
                <img id='iLogoCroix' src='img/bandeau/croix_caducee.png' alt=''>
                <h1><?= NOM_PHARMA ?></h1>
                <strong><?= LOC_PHARMA_BND ?></strong>
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
