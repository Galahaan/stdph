<?php

session_start();

// Si on arrive ici, c'est que le lien du pixel transparent de l'en-tête a été suivi,
// c'est donc soit un robot de navigateur, soit un aspirateur ...
//
// maintenant, pour faire la différence, on va suivre ces 3 étapes :

// 1) on récupère l'adresse ip du visiteur :

if( isset($_SERVER['HTTP_CLIENT_IP']) ){
    $ip = $_SERVER['HTTP_CLIENT_IP']; // IP si internet partagé
}
elseif( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ){
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; // IP derrière un proxy
}
else {
    $ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''); // IP 'normale'
}

// 2) on inclut la fonction qui contient la liste des ip des vrais moteurs de recherche

include('inclus/ipRobots.php');

// 3) et on teste notre visiteur ...
//    - si c'est un aspirateur
//        => on crée une variable de session
//        => et on le renvoie vers l'accueil pour le bloquer et lui afficher un message (cf en-tete.php)
//    - sinon, on renvoie simplement vers l'accueil, ce qui lui permettra de continuer son cheminement

if( !isMoteur($ip) ) {
    $_SESSION['isAspi'] = true;
}

// retour vers l'accueil
header('Location: index.php');

?>
