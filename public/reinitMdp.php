<?php

include('inclus/enteteP.php');

require_once('./inclus/initDB.php');

// ============================================================================================================
//
// On arrive ici à partir de la page 'connexion', en cliquant sur le bouton 'mot de passe oublié'.
//
// Description du fonctionnement de cette page
//
// - l'utilisateur saisit alors le mail qu'il avait choisi le jour de la création de son compte
//
// - si le mail est bien dans la BDD, on l'utilise pour lui envoyer un code de réinitialisation,
//   ainsi qu'un lien vers la page où il devra saisir son mail et ce code.
//   (plus besoin de variable de session pour identifier l'utilisateur !)
//
// - lorsque l'utilisateur clique sur le lien fourni dans le mail, il peut alors saisir son mail et son code
//
// - si le code correspond bien à celui stocké en BDD, on affiche alors un nouveau formulaire dans lequel
//   le client doit saisir et confirmer son nouveau mdp
//   (c'est le seul moment où l'on utilise une petite variable de session, juste pour garder le mail d'un
//    formulaire à l'autre, pour enregistrer le nouveau mdp au bon utilisateur)
//
// On n'oublie pas, en cas de connexion réussie, (qui reste possible, même en cours de procédure
// 'mot de passe oublié'), d'annuler les infos liées à cette procédure : la petite variable de session et
// le code de réinitialisation.
//
// On n'oublie pas non plus, lorsque l'utilisateur va suivre le lien du mail pour saisir son code,
// d'interrompre la procédure 'mot de passe oublié' dans le cas où, au même moment, un autre utilisateur
// a déjà une session en cours.
//
//
// Variante (initiale) :
// Lors de l'envoi du mail au user étourdi, on lui donnait un simple lien à suivre, contenant en
// paramètre GET un code de réinitialisation. MAIS, il fallait aussi, à partir de ce moment, être
// toujours dans la même session, dans laquelle on avait stocké le mail de l'utilisateur, pour
// pouvoir l'identifier et le relier au code.
// C'était donc plus rapide pour lui, mais moins sécurisé ...
//
// ============================================================================================================

// Pour être sympathique à l'utilisateur, s'il a déjà validé son mail dans la page connexion,
// avant de déclencher la procédure 'mot de passe oublié', on le lui pré-remplit dans le formulaire
if( isset($_GET['mail']) && ! empty($_GET['mail']) && mailValide($_GET['mail']) ){
    $mail = $_GET['mail'];
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Traitement du formulaire contenant le mail
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

if( isset($_POST['validerMail']) && !empty($_POST['mail']) ){

    if( mailValide($_POST['mail']) ){
        $mail = $_POST['mail']; // maintenant on se base sur la donnée validée par le user
                                // (on écrase le mail issu de GET)

        // récupération de l'ID utilisateur via son mail
        $phraseRequete = 'SELECT id, prenom from ' . TABLE_CLIENTS . " WHERE mail='" . $mail . "'";
        $requete = $dbConnex->prepare($phraseRequete);
        if( $requete->execute() ){

            $res = $requete->fetch();
            $id = $res['id'];
            $prenom = $res['prenom'];

            if( !empty($id) ){

                // OK, le mail correspondait bien à qqn de connu en BDD, le but est maintenant de :
                // - générer, puis stocker en BDD, un code aléatoire qui permettra à l'utilisateur de re-définir son mot de passe
                // - envoyer un mail, contenant ce code, à l'utilisateur

                // génération aléatoire du code
                $code = genCode(NB_CAR_CODE_ALEA);

                // stockage en BDD
                $phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET rst='" . $code . "' WHERE id=" . $id;
                $requete = $dbConnex->prepare($phraseRequete);

                // si la requête s'est bien passée, on prévient l'utilisateur par mail
                if( $requete->execute() == true ){

                    // => on envoie un mail de confirmation au client
                    $objet       = "procédure \"mot de passe oublié\"";
                    $rc = "\r\n";
                    $messageTxt  =  "Bonjour " . $prenom . "," . $rc.$rc .
                                    "Suite à votre demande sur " . PHIE_URLC . " , nous vous communiquons " . $rc .
                                    "le code qui vous permettra de définir un nouveau mot de passe : &nbsp;&nbsp;&nbsp;" .
                                    "<b>" . $code . "</b>" . $rc.$rc .
                                    "Veuillez le saisir, ainsi que votre mail, dans le formulaire d'" .
                                    "<a href='" . SW_ADRESSE_SITE_PHARMACIE . "reinitMdp.php?mc=code'><u>identification</u></a>.". $rc.$rc .
                                    "Cordialement," . $rc .
                                    "Le service technique";
                    $messageHtml = $messageTxt; // un jour on fera un joli message HTML !
                    if( mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, $mail, $objet, $messageTxt, $messageHtml) ){

                        $confirmation = "<div class='cMessageConfirmation'>" .
                                            "<p id='iFocus'>Votre demande a bien été prise en compte.</p>" .
                                            "<p>Vous allez recevoir d'ici peu un mail vous permettant de définir un nouveau mot de passe.</p>" .
                                        "</div>";
                    }
                    else{
                        $confirmation = "<div class='cMessageConfirmation'>" .
                                            "<p id='iFocus'>Aïe, il y a eu un problème lors de l'envoi du mail vous permettant de définir un nouveau mot de passe.</p>" .
                                            "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
                                        "</div>";
                    }
                }
                else{
                    // le code 'rst' n'a pas pu être stocké en BDD
                    $confirmation = "<div class='cMessageConfirmation'>" .
                                        "<p id='iFocus'>Aïe, le serveur est apparemment indisponible.</p>" .
                                        "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
                                        "<p>(code indéfini)</p>" .
                                    "</div>";
                }
            }
            else{
                // le mail n'existe pas en base
                $erreur = "ce mail est inconnu";
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
    else{
        // le mail est invalide
        $erreur = "le mail est invalide";
    }
}


/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Test du code de réinitialisation
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

if( isset($_POST['validerCode']) && !empty($_POST['mail']) && !empty($_POST['code']) ){

    if( mailValide($_POST['mail']) ){
        $mail = $_POST['mail'];

        if( strlen($_POST['code']) == strlen(strip_tags($_POST['code'])) ){
            $codeUser = strip_tags($_POST['code']);

            // on veut vérifier que le code est bien associé au mail
            $phraseRequete = 'SELECT rst FROM ' . TABLE_CLIENTS . " WHERE mail='" . $mail . "'";
            $requete = $dbConnex->prepare($phraseRequete);
            if( $requete->execute() ){

                $res = $requete->fetch();
                if( isset($res['rst']) && !empty($res['rst']) ){

                    // on n'a plus qu'à comparer les 2 codes
                    if( $codeUser == $res['rst'] ){
                        // l'utilisateur est bien identifié, on le renvoie sur le formulaire 'mdp'
                        $_SESSION['tmp']['mail']  = $mail;
                        header('Location: reinitMdp.php?mc=mdp');
                    }
                    else{
                        // les codes sont différents
                        $erreur = "code incorrect";
                    }
                }
                else{
                    // aucun code 'rst' n'est défini pour ce mail, mais on fait un message plus évasif
                    $erreur = "aucune correspondance entre ce mail et ce code";
                }
            }
            else{
                // le code 'rst' n'a pas pu être lu en BDD
                $confirmation = "<div class='cMessageConfirmation'>" .
                                    "<p id='iFocus'>Aïe, le serveur est apparemment indisponible.</p>" .
                                    "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
                                    "<p>(code invérifiable)</p>" .
                                "</div>";
            }
        }
        else{
            $erreur = "code invalide";
        }
    }
    else{
        $erreur = "mail invalide";
    }
}


/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Traitement du formulaire contenant le nouveau mdp et sa confirm
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

if( isset($_POST['validerMdp']) && !empty($_POST['mdp']) && !empty($_POST['mdpc']) && !empty($_SESSION['tmp']['mail']) ){

    if( mdpValide($_POST['mdp']) && mdpValide($_POST['mdpc']) ){
        $mdp  = $_POST['mdp'];
        $mdpc = $_POST['mdpc'];

        if( $mdp == $mdpc ){

            // on crypte le nouveau mdp avant de le stocker en BDD
            $mdpCrypte = password_hash($mdp, PASSWORD_DEFAULT);

            // on le stocke
            $phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET pwd='" . $mdpCrypte . "' WHERE mail='" . $_SESSION['tmp']['mail'] . "'";
            $requete = $dbConnex->prepare($phraseRequete);
            if( $requete->execute() ){
                $confirmation = "<div class='cMessageConfirmation'>" .
                                    "<p id='iFocus'>Votre nouveau mot de passe a bien été enregistré.</p>" .
                                "</div>";

                // dans un souci de sécurité, on supprime le code en BDD, et on termine la session
                $phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET rst='' WHERE mail='" . $_SESSION['tmp']['mail'] . "'";
                $requete = $dbConnex->prepare($phraseRequete);
                $requete->execute();
                // je ne fais pas de test de succès, parce qu'au pire, c'est pas très grave

                // enfin, je termine la session :
                unset($_SESSION['tmp']);
                session_destroy();
            }
            else{
                // le nouveau mdp n'a pas pu être stocké en BDD
                $confirmation = "<div class='cMessageConfirmation'>" .
                                    "<p id='iFocus'>Aïe, le serveur est apparemment indisponible.</p>" .
                                    "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
                                    "<p>(mdp non enregistré)</p>" .
                                "</div>";
            }
        }
        else{
            $erreur = "les 2 mots de passe sont différents";
        }
    }
    else{
        $erreur = "mot de passe invalide (de " . NB_CAR_MIN_MDP . " à " . NB_CAR_MAX_MDP . " car. dont 1 chiffre, 1 majuscule, 1 minuscule)";
    }
}


/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Procédure interférant dans une session en cours
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

// on a suivi le lien contenu dans le mail (avec le code de réinitialisation), mais ...
// une session est déjà en cours !
if( isset($_GET['mc']) && $_GET['mc'] == 'code' && isset($_SESSION['client']) ){

    $confirmation = "<div class='cMessageConfirmation'>" .
                        "<p id='iFocus'>" . $_SESSION['client']['prenom'] . " a actuellement une session en cours. La procédure ne pourra se poursuivre qu'après sa déconnexion ...</p>" .
                    "</div>";
}


include('inclus/enteteH.php');
?>
	<main id='iMain'>
        <section id='iRMdP' class='cSectionContour'><h2>Mot de passe oublié</h2>

            <?php if( isset($confirmation) ) : // ce sont tous les cas où l'on a validé l'un ou l'autre des formulaires ?>

                <?= $confirmation; ?>

            <?php elseif( isset($_GET['mc']) && $_GET['mc'] == 'code' ) : // on arrive sur la page depuis le lien du mail ?>

            <p>Veuillez saisir votre mail et le code que vous avez reçu ...</p>
            <form method='POST'>
                <div class='cChampForm'>
                    <label for='iMail'>mail</label>
                    <input type='email' id='iMail' name='mail' required placeholder='...' autofocus >
                </div>
                <div class='cChampForm'>
                    <label for='iCode'>code</label>
                    <input type='text' id='iCode' name='code' required placeholder='...' >
                    <?= isset($erreur) ? "<sub>".$erreur."</sub>" : "" ?>
                </div>
                <div id='iValider'>
                    <button class='cDecoBoutonValid' name='validerCode'>valider</button>
                </div>
            </form>

            <?php elseif( isset($_GET['mc']) && $_GET['mc'] == 'mdp' && !empty($_SESSION['tmp']['mail'])) : // l'utilisteur a bien été identifié ?>

            <p>Veuillez définir votre nouveau mot de passe ...</p>
            <form method='POST'>
                <div class='cChampForm'>
                    <label for='iMdp'>mot de passe</label>
                    <input type='password' id='iMdp' name='mdp' required placeholder='...' autofocus >
                </div>
                <div class='cChampForm'>
                    <label for='iMdpc'>confirmation</label>
                    <input type='password' id='iMdpc' name='mdpc' required placeholder='...' >
                    <?= isset($erreur) ? "<sub>".$erreur."</sub>" : "" ?>
                </div>
                <div id='iValider'>
                    <button class='cDecoBoutonValid' name='validerMdp'>valider</button>
                </div>
            </form>

            <?php else : ?>

            <p>Veuillez saisir le mail que vous aviez utilisé lors de votre inscription ...</p>
            <form method='POST'>
                <div class='cChampForm'>
                    <label for='iMail'>mail</label>
                    <input type='email' id='iMail' name='mail' required placeholder='...' autofocus value='<?= isset($mail) ? $mail : "" ?>'>
                    <?= isset($erreur) ? "<sub>".$erreur."</sub>" : "" ?>
                </div>
                <div id='iValider'>
                    <button class='cDecoBoutonValid' name='validerMail'>valider</button>
                </div>
            </form>

            <?php endif ?>

        </section>
	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>