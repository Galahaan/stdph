<?php

session_start(); // en début de chaque fichier utilisant $_SESSION

ini_set("display_errors", 1);  // affichage des erreurs - à virer à la mise en prod !

unset($_SESSION['client']);
session_destroy();

header('Location: index.php');

// EXPLICATIONS :
// on a différentes façons de finir la session.
//
// - unset($_SESSION['client']);
//         => vide le contenu de la variable concernée, ie de l'élément 'client', mais la variable $_SESSION existe toujours en soi.
//            donc par ex. $_SESSION['truc'] existerait toujours
//
// - unset($_SESSION);
//         => en théorie c'est plus violent : la variable $_SESSION devient NULL
//
// - session_destroy();
//         => en théorie ça semble correspondre à la destruction de l'adresse de la variable $_SESSION,
//            sans toucher à son contenu (mais qui est donc devenu inaccessible !)
//
// Le hic, c'est que, après avoir testé chaque possibilité une par une, suivie d'un var_dump($_SESSION),
// puis de 'précédent' avec mon navigateur (Firefox) ...
//
// - unset($_SESSION['client'])
//         => var_dump affiche   array(0){}    => donc a priori c'est normal !
//         => précédent affiche  plus d'utilisateur connecté, MAIS accès aux pages ordonnance, commande et mon-compte, mais tout est vide.
//                                                            => ça c'est pas normal ! puisque je fais un isset($_SESSION['client'])
//
// - unset($_SESSION)
//
//         => var_dump affiche   NULL          => donc a priori c'est normal !
//         => précédent affiche  absolument TOUT ! comme si on ne s'était pas déconnecté !.. => ça c'est pas normal !
//
// - session_destroy()
//         => var_dump affiche   array(1) { ["client"]=> ... tout le contenu !
//                               bon, étant donné qu'il n'y a aucun code entre session_destroy() et var_dump(), on peut imaginer
//                               qu'en allant à la même adresse, on retrouve les infos, mais bon, c'est bof !
//                                                         
//         => précédent affiche  plus aucun accès, on est vraiment déconnecté ... ça c'est bien !
//
//  CONCLUSION : je fais un mix des solutions 1 et 3

?>