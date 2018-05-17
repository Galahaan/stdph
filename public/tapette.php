<?php

session_start();

// Si on arrive ici, c'est que le lien du pixel transparent de l'en-tête a été suivi,
// c'est donc soit un robot de navigateur, soit un aspirateur ...
//
// maintenant, pour faire la différence, on va suivre ces qqs étapes :

// 1) on récupère l'adresse IP du visiteur :
if( isset($_SERVER['HTTP_CLIENT_IP']) ){
    $ip1 = $_SERVER['HTTP_CLIENT_IP']; // IP si internet partagé
}
elseif( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ){
    $ip1 = $_SERVER['HTTP_X_FORWARDED_FOR']; // IP derrière un proxy
}
else {
    $ip1 = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''); // IP 'normale'
}


// 2) on remonte au nom de domaine
$do1 = gethostbyaddr($ip1);


// 3) on vérifie l'IP de ce nom de domaine, puis le nom de domaine de cette IP (!)
$ip2 = gethostbyname($do1);
$do2 = gethostbyaddr($ip2);

// 4) - si les 2 IP sont différentes, on décide que c'est un aspirateur :
//        => on place l'info (true) dans une variable de session
//        => et on le renvoie vers l'accueil, mais grâce à la variable de session,
//           il sera bloqué et n'aura plus accès qu'à un simple message (cf enteteXX.php)
//    - sinon, on renvoie simplement vers l'accueil, ce qui lui permettra de continuer son cheminement
if( $ip1 != $ip2 ){
    $_SESSION['bot']['isAspi'] = true;
}


// juste avant de retourner à l'accueil, on sauvegarde les infos dans la session :
$_SESSION['bot']['ip1'] = $ip1;
$_SESSION['bot']['do1'] = $do1;
$_SESSION['bot']['ip2'] = $ip2;
$_SESSION['bot']['do2'] = $do2;

$_SESSION['bot']['url']        = "bigouig";
$_SESSION['bot']['mailDest']   = "clr.tstph@use.startmail.com";
$_SESSION['bot']['mailExp']    = "bigouigfiy@cluster020.hosting.ovh.net";


// retour vers l'accueil
header('Location: index.php');

?>

