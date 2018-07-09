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

// pour personnaliser l'entete en fonction de la page qui l'a appelé
// (appel d'un CDN, refresh de la page, positionnement d'un focus, ...)
$enteteSpecs = enteteSpecs($_SERVER['REQUEST_URI']);

?>