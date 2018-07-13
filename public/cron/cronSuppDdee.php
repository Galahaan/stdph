<?php

// dans la doc OVH sur les tâches cron, ils indiquent qu'il faut mettre les chemins en absolu,
// et que la variable __DIR__ correspond justement au début du chemin :
// https://docs.ovh.com/fr/hosting/mutualise-taches-automatisees-cron/
require_once(__DIR__ . '/../inclus/fonctions.php');
require_once(__DIR__ . '/../inclus/initDB.php');


// clients ayant explicitement demandé la suppression de leur compte :
// au bout de XX jours => on les supprime effectivement de la BDD
$phraseRequete = "SELECT id, prenom, nom, mail FROM " . TABLE_CLIENTS . " WHERE supprDdee=1 AND DATE_ADD(dateConx, INTERVAL 30 DAY) <= now()";
$requete = $dbConnex->prepare($phraseRequete);
if( $requete->execute() != true ){ $erreurRequete = true; }
//pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD
$clients = $requete->fetchAll();


foreach ($clients as $client) {

    // je supprime le compte :
    $phraseRequete = "DELETE FROM " . TABLE_CLIENTS . " WHERE id=" . $client['id'];
    $requete = $dbConnex->prepare($phraseRequete);
    if( $requete->execute() != true ){ $erreurRequete = true; }
    //pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD

    // ... et je laisse une trace dans le journal (log) accessible sur OVH
    // (https://logs.cluster020.hosting.ovh.net/bigouig.fr/)
    echo $rc . "Suppression du compte (demandee par le client) - " .
          $client['prenom'] . " " . $client['nom'] . " (" . $client['mail'] . ")" . $rc;
}
?>
