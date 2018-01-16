<?php

session_start(); // en début de chaque fichier utilisant $_SESSION

// ATTENTION :
// on ne ferme la session que pour l'élément 'utilisateur', de cette façon, on a toujours accès à d'autres
// éléments de la variable $_SESSION si besoin.
unset($_SESSION['client']);

// Pour être plus destructeur, on aurait pu faire directement :
//unset($_SESSION);

// et pour faire encore pire, il faudrait détruire la session avec un session_destroy() ou un truc du genre


header('Location: index.php');

?>