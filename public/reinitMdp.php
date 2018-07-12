<?php

include('inclus/enteteP.php');

require_once('./inclus/initDB.php');

// à l'arrivée sur cette page, si l'utilisateur avait validé son mail sur la page précédente, on le lui pré-remplit :
if( !empty($_GET['mail']) ){

    if( mailValide($_GET['mail']) ){ $mail = $_GET['mail']; }
        else{ $mail = ''; }
}

// traitement du formulaire
if( isset($_POST['valider']) && !empty($_POST['mail']) ){
    if( mailValide($_POST['mail']) ){
        $mail = $_POST['mail']; // à la 1ère validation du form, on écrase le $mail qui venait du GET

        // le but est maintenant de remplacer, en BDD, l'ancien mot de passe de l'utilisateur par un nouveau,
        // temporaire, généré aléatoirement, puis de prévenir l'utilisateur par mail

        // 1 - récupération de l'ID utilisateur via son mail
        $phraseRequete = 'SELECT id from ' . TABLE_CLIENTS . " WHERE mail='" . $mail . "'";
        $requete = $dbConnex->prepare($phraseRequete);
        if( $requete->execute() ){

            $res = $requete->fetch();
            $id = $res['id'];

            if( !empty($id) ){

                // 2 - génération aléatoire du mot de passe
                $mdpTmp = genererMdp();

                // 3 - cryptage du mdp temporaire
                $mdpCrypte = password_hash($mdpTmp, PASSWORD_DEFAULT);

                // 4 - modification en BDD du mot de passe utilisateur
                $phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET pwd='" . $mdpCrypte . "', pwdStatus='reset' WHERE id=" . $id;
                echo "requête = " . $phraseRequete . "<br>";
                $requete = $dbConnex->prepare($phraseRequete);
                
                // 5 - si la requête s'est bien passée, on prévient l'utilisateur par mail
                if( $requete->execute() == true ){

                    // => on envoie un mail de confirmation au client
                    $objet       = "Votre accès a été réinitialisé ...";
                    $rc = "\r\n";
                    $messageTxt  =  "Bonjour " . $_SESSION['client']['prenom'] . "," . $rc.$rc .
                                    "À votre demande, nous avons réinitialisé votre mot de passe sur " . PHIE_URLC . " ." . $rc .
                                    "Voici votre mot de passe temporaire :" . $rc.$rc .
                                    $mdpTmp . $rc.$rc .
                                    "Attention, il n'est valable que pour la prochaine connexion et devra être aussitôt modifié." . $rc.$rc .
                                    "Cordialement," . $rc .
                                    "Le service technique";
                    $messageHtml = $messageTxt; // un jour on fera un joli message HTML !
        //            mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, $mail, $objet, $messageTxt, $messageHtml);
                    if( mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, MAIL_DEST_CLR, $objet, $messageTxt, $messageHtml) ){

                        $confirmation = "<div class='cMessageConfirmation'>" .
                                            "<p id='iFocus'>Votre mot de passe a bien été réinitialisé. Vous allez le recevoir par mail.</p>" .
                                            "<p>Attention : il n'est valable qu'une seule fois, vous devrez donc le changer dès la prochaine connexion.</p>" .
                                        "</div>";
                    }
                    else{
                        $confirmation = "<div class='cMessageConfirmation'>" .
                                            "<p id='iFocus'>Aïe, il y a eu un problème lors de l'envoi du mot de passe ...</p>" .
                                            "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
                                        "</div>";
                    }
                }
                else{
                    // le nouveau mdp temporaire n'a pas pu être stocké en BDD
                    $confirmation = "<div class='cMessageConfirmation'>" .
                                        "<p id='iFocus'>Aïe, le serveur est apparemment indisponible.</p>" .
                                        "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
                                        "<p>(mdp tmp non stocké)</p>" .
                                    "</div>";
                }
            }
            else{
                // le mail n'existe pas en base
                $confirmation = "<div class='cMessageConfirmation'>" .
                                    "<p id='iFocus'>Désolé, ce mail est inconnu.</p>" .
                                "</div>";
            }
        }
        else{
            // erreur accès BDD (id indéterminé)
            $confirmation = "<div class='cMessageConfirmation'>" .
                                "<p id='iFocus'>Aïe, le serveur est apparemment indisponible.</p>" .
                                "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
                                "<p>(user id indéterminé)</p>" .
                            "</div>";
        }
    }
}

include('inclus/enteteH.php');
?>
	<main id='iMain'>
        <section id='iRMdP' class='cSectionContour'><h2>Veuillez confirmer votre mail svp</h2>

            <?= isset($confirmation) ? $confirmation : "" ?>

            <form method='POST'>
                <div class='cChampForm'>
                    <label for='iMail'>mail</label>
                    <input type='email' id='iMail' name='mail' required placeholder='...' autofocus value='<?= isset($mail) ? $mail : "" ?>'>
                </div>
                <div id='iValider'>
                    <button class='cDecoBoutonValid' name='valider'>valider</button>
                </div>
            </form>
        </section>
	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>