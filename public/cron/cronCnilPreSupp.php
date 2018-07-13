<?php

// dans la doc OVH sur les tâches cron, ils indiquent qu'il faut mettre les chemins en absolu,
// et que la variable __DIR__ correspond justement au début du chemin :
// https://docs.ovh.com/fr/hosting/mutualise-taches-automatisees-cron/
require_once(__DIR__ . '/../inclus/fonctions.php');
require_once(__DIR__ . '/../inclus/initDB.php');


// clients dont la dernière connexion remonte à au moins [1 an - DELAI_AV_SUPPR jours] : on envoie un mail de pré-suppression
$phraseRequete = "SELECT mail, prenom, nom, DATE_FORMAT(dateConx, '%d/%m/%Y') AS date FROM " . TABLE_CLIENTS . " WHERE DATE_ADD(dateConx, INTERVAL " . intval(365 - DELAI_AV_SUPPR) . " DAY) <= now()";
$requete = $dbConnex->prepare($phraseRequete);
if( $requete->execute() != true ){ $erreurRequete = true; }
//pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD
$clients = $requete->fetchAll();

foreach ($clients as $client) {
    // j'envoie un mail à chaque client concerné pour le prévenir de la suppression imminente de son compte ...
    $objet       = "Suppression imminente de votre compte (J - " . DELAI_AV_SUPPR . ")";
    $rc          = "\r\n";
    $messageTxt  = "Bonjour " . $client['prenom'] . "," . $rc.$rc .
                   "Votre pharmacie s'est engagée, auprès de la CNIL (<b>C</b>ommission <b>N</b>ationale de l'<b>I</b>nformatique et des <b>L</b>ibertés), " . $rc .
                   "à ne pas conserver les données personnelles d'un compte inactif pendant plus d'un an." . $rc .
                   "(c'est à dire sans connexion durant cette période)" . $rc .
                   "Or votre dernière connexion sur <b>&nbsp;&nbsp;" . PHIE_URLC . "&nbsp;&nbsp;</b> remonte au " . $client['date'] . "." . $rc .
                   "Sans nouvelle connexion de votre part, votre compte sera donc supprimé dans " . DELAI_AV_SUPPR . " jours." . $rc.$rc.
                   "Cordialement," . $rc .
                   "Le service technique";
    $messageHtml = $messageTxt; // un jour on fera un joli message HTML !
//    mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, $client['mail'], $objet, $messageTxt, $messageHtml);
    mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, MAIL_DEST_CLR, $objet, $messageTxt, $messageHtml);


    // ... et je laisse une trace dans le journal (log) accessible sur OVH
    // (https://logs.cluster020.hosting.ovh.net/bigouig.fr/)
    echo $rc . "Rappel avant suppression du compte (" . DELAI_AV_SUPPR . " jours) - " .
          $client['prenom'] . " " . $client['nom'] . " (" . $client['mail'] . ") - dern. conx. " . $client['date'] . $rc;
}
?>
