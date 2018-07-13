<?php

// dans la doc OVH sur les tâches cron, ils indiquent qu'il faut mettre les chemins en absolu,
// et que la variable __DIR__ correspond justement au début du chemin :
// https://docs.ovh.com/fr/hosting/mutualise-taches-automatisees-cron/
require_once(__DIR__ . '/../inclus/fonctions.php');
require_once(__DIR__ . '/../inclus/initDB.php');


// clients dont la dernière connexion remonte à 1 an ou plus : on les prévient, et on les supprime
$phraseRequete = "SELECT id, mail, prenom, DATE_FORMAT(dateConx, '%d/%m/%Y') AS date FROM " . TABLE_CLIENTS . " WHERE DATE_ADD(dateConx, INTERVAL 1 YEAR) <= now()";
$requete = $dbConnex->prepare($phraseRequete);
if( $requete->execute() != true ){ $erreurRequete = true; }
//pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...
$clients = $requete->fetchAll();


foreach ($clients as $client) {

    // je supprime le compte :
    $phraseRequete = "DELETE FROM " . TABLE_CLIENTS . " WHERE id=" . $client['id'];
    $requete = $dbConnex->prepare($phraseRequete);
    if( $requete->execute() != true ){
        $erreurRequete = true;
        //pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD
    }
    else{
        // j'envoie un mail à chaque client concerné pour le prévenir de la suppression de son compte ...
        $objet       = "Suppression de votre compte";
        $rc          = "\r\n";
        $messageTxt  = "Bonjour " . $client['prenom'] . "," . $rc.$rc .
                       "Votre dernière connexion sur " . PHIE_URLC . " date du " . $client['date'] . "." . $rc .
                       "Conformément à l'engagement, pris par votre pharmacie auprès de la CNIL, " . $rc .
                       "de ne pas conserver les données personnelles d'un compte inactif pendant plus d'un an, " . $rc .
                       "votre compte vient d'être supprimé." . $rc.$rc .
                       "Cordialement," . $rc .
                       "Le service technique";
        $messageHtml = $messageTxt; // un jour on fera un joli message HTML !
    //    mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, $client['mail'], $objet, $messageTxt, $messageHtml);
        mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, MAIL_DEST_CLR, $objet, $messageTxt, $messageHtml);


        // ... et je laisse une trace dans le journal (log) accessible sur OVH
        // (https://logs.cluster020.hosting.ovh.net/bigouig.fr/)
        echo $rc . "Suppression du compte (pour inactivite) - " .
              $client['prenom'] . " " . $client['nom'] . " (" . $client['mail'] . ") - dern. conx. " . $client['date'] . $rc;
    }
}
?>
