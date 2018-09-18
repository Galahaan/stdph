<?php

include("inclus/enteteP.php");

// Si le nom de la page est saisi directement dans la barre d'adresse, alors
// que la personne ne s'est pas encore connectée => retour accueil direct !
if( !isset($_SESSION['client']) ){
    header('Location: index.php');
}

// pour les accès à la BDD
require_once("./inclus/initDB.php");

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Demande d'envoi d'un code de validation (modification ou suppression des données)
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

// But du § : - générer un code aléatoire
//            - initialiser le nombre de tentatives de validation de ce code (NB_MAX_ESSAIS_CODE)
//            - le stocker en BDD, ainsi que sa date de validité
//            - l'envoyer par mail à l'utilisateur

if( isset($_POST['demanderCode']) ){

    // génération aléatoire du code
    // (la div. entière par 2 dans openssl_... est due à bin2hex qui 'multiplie par 2' le nb de car. initial)
    $codeBinaire = openssl_random_pseudo_bytes( intdiv(NB_CAR_CODE_MODIF, 2) );
    $codeModif   = bin2hex($codeBinaire);

    // date de validité du code (en secondes depuis le 01/01/1970)
    $codeDateV = time() + DUREE_VALID_CODE_MODIF * 60;

    $_SESSION['client']['nbEssaisCodeRestants'] = NB_MAX_ESSAIS_CODE;

    // récupération en BDD de l'ID de l'utilisateur
    $phraseRequete = "SELECT id FROM " . TABLE_CLIENTS . " WHERE mail='" . $_SESSION['client']['mail'] . "'";
    $requete = $dbConnex->prepare($phraseRequete);
    $requete->execute();
    $res = $requete->fetch();
    $id = $res['id'];

    // stockage en BDD du code + de la date de validité
    $erreurRequete = false;
    $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='" . $codeModif . "', codeDateV='" . $codeDateV . "' WHERE id =" . $id;
    $requete = $dbConnex->prepare($phraseRequete);
    if( $requete->execute() != true ){ $erreurRequete = true; }
    //pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...

    // envoi du code par mail
    $objet       = "Procédure sécurisée pour la modification de vos données";

    $rc = "\r\n";
    $messageTxt  =  "Bonjour " . $_SESSION['client']['prenom'] . "," . $rc.$rc .
                    "Suite à votre demande, vous disposez de " . DUREE_VALID_CODE_MODIF . " mn pour saisir le code suivant " .
                    "sur le site " . PHIE_URLC . " : " . $rc .
                    $codeModif . $rc .
                    "Vous pourrez alors modifier ou supprimer vos données personnelles." . $rc.$rc .
                    "N.B. : Le code deviendra invalide dans les cas suivants :" . $rc .
                    "- après la 1ère utilisation de ce code" . $rc .
                    "- au bout de " . NB_MAX_ESSAIS_CODE . " essais infructueux" . $rc .
                    "- si le temps imparti est écoulé" . $rc .
                    "- si un ou plusieurs autres codes ont été demandés" . $rc.$rc .
                    "Cordialement," . $rc .
                    "Le service technique";

    $messageHtml = $messageTxt; // un jour on fera un joli message HTML !

    if( $erreurRequete == false ){
        if( mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, $_SESSION['client']['mail'], $objet, $messageTxt, $messageHtml)){
            $confirmEnvoiCode =
                "<div class='cMessageConfirmation'>" .
                        "<p id='iFocus'>Le mail contenant le code vient de vous être envoyé.</p>" .
                        "<p>Attention : selon les cas, il peut être dirigé dans les messages indésirables ('spams').</p>" .
                        "<p>Si vous n'avez pas reçu le mail après avoir patienté, demandez un nouveau code.</p>" .
                "</div>";
        }
        else{
            $confirmEnvoiCode =
                "<div class='cMessageConfirmation'>" .
                        "<p id='iFocus'>Aïe, il y a eu un problème lors de l'envoi du code ...</p>" .
                        "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
                "</div>";
        }
    }
    else{
        $confirmEnvoiCode =
            "<div class='cMessageConfirmation'>" .
                    "<p id='iFocus'>Aïe, il y a eu un problème lors de la génération du code ...</p>" .
                    "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
            "</div>";
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Test du code de validation
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

// But du § : - récupérer, en BDD, le code et sa date de validité
//            - si la date est ok, comparer le code avec celui du formulaire
//            - si le code est ok, autoriser la modif des données
//            - mais si plus de X essais infructueux, on bloque
// NB :
// - j'utilise la variable $_SESSION pour stocker le nombre d'essais infructueux lors
//   de la validation du code
//   (mais attention, il ne faut pas oublier de le remettre à 0 pour tout nouveau code,
//    ie dans le formulaire précédent de demande de code !)
//
// - de même, une fois le code validé, j'utilise $_SESSION pour stocker l'autorisation
//   et la date de validité, de cette façon, si l'utilisateur fait des erreurs de
//   saisie dans le formulaire de ses données (mail, mdp, ...), il pourra re-valider
//   le formulaire de modif sans avoir à redemander un code.
//   En revanche, une fois la date de validité expirée, il faudra redemander un code.

if( isset($_POST['validerCode']) ){

    // récupération en BDD de l'ID de l'utilisateur
    $phraseRequete = "SELECT id FROM " . TABLE_CLIENTS . " WHERE mail='" . $_SESSION['client']['mail'] . "'";
    $requete = $dbConnex->prepare($phraseRequete);
    $requete->execute();
    $res = $requete->fetch();
    $id = $res['id'];

    // récupération en BDD du code, et de sa date de validité
    $phraseRequete = "SELECT codeModif, codeDateV FROM " . TABLE_CLIENTS . " WHERE id='" . $id . "'";
    $requete = $dbConnex->prepare($phraseRequete);
    $requete->execute();
    $res = $requete->fetch();
    $codeBDD = $res['codeModif'];
    $_SESSION['client']['codeDateV'] = $res['codeDateV'];

    if( (time() >= $_SESSION['client']['codeDateV']) || ($_SESSION['client']['nbEssaisCodeRestants'] <= 0) ){
        // date validité expirée   ou   trop de tentatives
        // => non seulement on ne donne pas l'accès aux modifs
        // => mais en plus on réinitiliase code + date validité

        $erreurRequete = false;
        $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='&#&##&#&', codeDateV='0' WHERE id =" . $id;
        $requete = $dbConnex->prepare($phraseRequete);
        if( $requete->execute() != true ){ $erreurRequete = true; }
        //pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...

        $confirmTestCode =  "<div class='cMessageConfirmation'>" .
                            "<p id='iFocus'>Le code est invalide ou expiré, mais vous pouvez en demander un nouveau.</p>" .
                            "</div>";
    }
    else{
        // date validité OK, mais code invalide => on ne donne pas l'accès aux modifs
        if( $_POST['code'] != $codeBDD ){
            $confirmTestCode =  "<div class='cMessageConfirmation'>" .
                                "<p id='iFocus'>Le code saisi est invalide.</p>" .
                                "</div>";
        }
        else{
            // date validité OK, et code valide => modifs autorisées
            $_SESSION['client']['mAutor'] = true;
            $confirmTestCode =  "<div class='cMessageConfirmation'>" .
                                "<p id='iFocus'>Vous pouvez maintenant modifier vos données ou supprimer votre compte.</p>" .
                                "</div>";
        }
    }
    $_SESSION['client']['nbEssaisCodeRestants'] -= 1; // c'est à la génération du code qu'on initialise cette variable
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Modification des données utilisateur
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

if( isset($_POST['validerModifs']) || isset($_POST['annulerModifs']) ){

    // Dans les 2 cas : données utilisateur à mettre à jour (valider), ou le code et sa validité à réinitialiser (annuler),
    // il y aura des infos à mettre à jour en BDD, on a donc besoin de l'ID de l'utilisateur.
    $phraseRequete = "SELECT id FROM " . TABLE_CLIENTS . " WHERE mail='" . $_SESSION['client']['mail'] . "'";
    $requete = $dbConnex->prepare($phraseRequete);
    $requete->execute();
    $res = $requete->fetch();
    $id = $res['id'];

    if( isset($_POST['annulerModifs']) ){
        // on a cliqué sur 'annuler'.
        // on réinitialise le code et sa durée de validité, pour la session, et en BDD
        $_SESSION['client']['mAutor'] = false;

        $erreurRequete = false;
        $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='&#&##&#&', codeDateV='0' WHERE id =" . $id;
        $requete = $dbConnex->prepare($phraseRequete);
        if( $requete->execute() != true ){ $erreurRequete = true; }
        //pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...

        // et pour ré-afficher les bonnes données (ie celles actuellement valides) :
        $_POST['mail']       = $_SESSION['client']['mail'];
        $_POST['telMobile']  = $_SESSION['client']['telMobile'];
    }
    else{
        // on a cliqué sur 'valider'.
        // Avant toute chose, si le code d'authentification est périmé, on sort !
        if( (time() < $_SESSION['client']['codeDateV']) ){

            // ********************************************        MAIL        *********************************************

            // Si le mail est différent => on modifie l'info en BDD, mais aussi, en cas de réussite, la globale SESSION
            if( !empty($_POST['mail']) && $_POST['mail'] != $_SESSION['client']['mail'] ){

                // le mail est bien différent du précédent, mais, juste avant de le stocker, on vérifie qu'il est valide
                if( mailValide($_POST['mail']) ){
                    $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET mail='" . $_POST['mail'] . "' WHERE id =" . $id;
                    $requete = $dbConnex->prepare($phraseRequete);
                    if( $requete->execute() == true ){
                        $modifOK[] = "mail";
                        $_SESSION['client']['mail'] = $_POST['mail'];
                    }
                    else{
                        $erreursBDD[] = "mail";
                    }
                }
                else{
                    $erreurs[] = "le mail est invalide.";
                }
            }

            // ********************************************       N° TEL       *********************************************

            // Si le n° de tel est différent => on modifie l'info en BDD, mais aussi, en cas de réussite, la globale SESSION
            if( $_POST['telMobile'] != $_SESSION['client']['telMobile'] ){

                // le n° est bien différent du précédent, mais, juste avant de le stocker, on vérifie qu'il est valide
                if( telValide($_POST['telMobile']) ){

                    $telMobile = formaterTel($_POST['telMobile']); // un espace entre chaque paire

                    // pour présenter à l'utilisateur le n° sous sa forme 'propre' (ie avec un espace entre chaque paire) :
                    $_POST['telMobile'] = $telMobile;

                    // stockage en BDD
                    $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET telMobile='" . $telMobile . "' WHERE id =" . $id;
                    $requete = $dbConnex->prepare($phraseRequete);
                    if( $requete->execute() == true ){
                        $modifOK[] = "n° de téléphone";
                        $_SESSION['client']['telMobile'] = $_POST['telMobile'];
                    }
                    else{
                        $erreursBDD[] = "n° de téléphone";
                    }
                }
                else{
                    $erreurs[] = "le n° de téléphone est invalide.<br>Il doit être composé de 5 paires de chiffres séparées ou non par des espaces.<br>ex. : 01 23 45 67 89";
                }
            }

            // ********************************************    MOT DE PASSE    *********************************************

            // Si les 3 champs du § 'Mot de passe' sont bien remplis, on lance les tests ...
            // Si tous les tests sont OK => on modifie l'info en BDD
            if( !empty($_POST['amdp']) || !empty($_POST['nmdp1']) || !empty($_POST['nmdp2']) ){
                if( !empty($_POST['amdp']) && !empty($_POST['nmdp1']) && !empty($_POST['nmdp2']) ){

                    // ____________________ 1e étape ____________________

                    // pour éviter les A/R inutiles avec la BDD, on commence par comparer les 2 nouveaux mdp
                    if( $_POST['nmdp1'] == $_POST['nmdp2'] ){

                        // ____________________ 2e étape ____________________

                        // on teste le nouveau mot de passe (de min à max car. dont 1 majuscule, 1 minuscule, 1 chiffre)
                        if( mdpValide($_POST['nmdp1']) ){

                            // ____________________ 3e étape ____________________

                            // on récupère l'ancien mot de passe en BDD
                            $phraseRequete = "SELECT pwd FROM " . TABLE_CLIENTS . " WHERE id='" . $id . "'";
                            $requete = $dbConnex->prepare($phraseRequete);
                            if( $requete->execute() == true ){

                                $res = $requete->fetch();
                                $mdpHash = $res['pwd'];

                                // ____________________ 4e étape ____________________

                                // on compare l'ancien mot de passe, saisi dans le formulaire, avec sa valeur récupérée en BDD
                                if( password_verify($_POST['amdp'], $mdpHash) ){

                                    // ____________________ 5e étape ____________________

                                    // puisque tout est OK, on stocke le nouveau mot de passe en BDD
                                    $nvMdpCrypte = password_hash($_POST['nmdp1'], PASSWORD_DEFAULT);
                                    $phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET pwd='" . $nvMdpCrypte . "' WHERE id =" . $id;
                                    $requete = $dbConnex->prepare($phraseRequete);

                                    // on remplit les 2 tableaux de messages (succès ou échec)
                                    if( $requete->execute() == true ){
                                        $modifOK[] = "mot de passe";
                                    }
                                    else{
                                        $erreursBDD[] = "mot de passe";
                                    }
                                }
                                else{
                                    $erreurs[] = "l'ancien mot de passe est incorrect.";
                                }
                            }
                            else{
                                $erreursBDD[] = "mot de passe (l'ancien n'a pas pu être vérifié)";
                            }
                        }
                        else{
                            $erreurs[] = "le mot de passe est invalide.<br>Il doit contenir entre " . NB_CAR_MIN_MDP . " et " . NB_CAR_MAX_MDP . " caractères dont 1 majuscule, 1 minuscule et 1 chiffre.";
                        }
                    }
                    else{
                        $erreurs[] = "le nouveau mot de passe et sa confirmation sont différents.";
                    }
                }
                else{
                    $erreurs[] = "pour modifier le mot de passe, les 3 cases doivent être renseignées.";
                }
            }

            // **************************    Mise en forme du message sur le bilan des modifs    ***************************

            // si on est dans un des 3 cas ci-dessous, on doit écrire un message d'information (modif validée ou échec)
            // => on peut donc déjà mettre les balises de début et de fin de ce message

            if( isset($modifOK) || isset($erreurs) || isset($erreursBDD) ){

                $confirmModifs = "<div class='cMessageConfirmation'><p id='iFocus'>";

                // Message pour les modifs validées
                if( isset($modifOK) ){
                    if( sizeof($modifOK) > 1 ){
                        $confirmModifs .= "Les éléments suivants ont bien été mis à jour :</p>" .
                                         "<ul>";
                        for( $i = 0; $i < sizeof($modifOK); $i++ ){
                            $confirmModifs .= "<li>" . $modifOK[$i] . "</li>";
                        }
                        $confirmModifs .= "</ul>";
                    }
                    else{
                        $confirmModifs .= "Votre " . $modifOK[0] . " a bien été mis à jour.</p>";
                    }

                    // transition avec le § éventuel suivant
                    if( isset($erreurs) || isset($erreursBDD) ){
                        $confirmModifs .= "<br><p>Mais attention : ";
                    }
                }

                // Message pour les erreurs utilisateur
                if( isset($erreurs) ){

                    // transition avec le § éventuel précédent
                    if( !isset($modifOK) ){
                        $confirmModifs .= "Attention : ";
                    }

                    if( sizeof($erreurs) > 1 ){
                        $confirmModifs .= "</p><ul>";
                        for( $i = 0; $i < sizeof($erreurs); $i++ ){
                            $confirmModifs .= "<li>" . $erreurs[$i] . "</li>";
                        }
                        $confirmModifs .= "</ul>";
                    }
                    else{
                        $confirmModifs .= $erreurs[0] . "</p>";
                    }
                }

                // Normalement très rare : message pour les erreurs serveur
                if( isset($erreursBDD) ){

                    // transition avec le § éventuel précédent
                    if( !isset($modifOK) && !isset($erreurs) ){
                        $confirmModifs .= "Aïe, le serveur est apparemment indisponible.</p>";
                    }
                    else{
                        $confirmModifs .= "<br><p>Et le serveur est apparemment indisponible.</p>";
                    }

                    if( sizeof($erreursBDD) > 1 ){
                        $confirmModifs .= "<ul>Les éléments suivants n'ont pas pu être enregistrés :";
                        for( $i = 0; $i < sizeof($erreursBDD); $i++ ){
                            $confirmModifs .= "<li>" . $erreursBDD[$i] . "</li>";
                        }
                        $confirmModifs .= "</ul>";
                    }
                    else{
                        $confirmModifs .= "<p>Votre " . $erreursBDD[0] . " n'a pas pu être enregistré.</p>";
                    }
                    $confirmModifs .= "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>";
                }
                $confirmModifs .= "</div>";
            }

            // Si tout s'est bien passé, ie que tous les champs modifiés ont bien été enregistrés,
            // => on réinitialise le code d'authentification et sa validité en BDD
            if( !isset($erreurs) && !isset($erreursBDD) ){

                $_SESSION['client']['mAutor'] = false;

                $erreurRequete = false;
                $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='&#&##&#&', codeDateV='0' WHERE id =" . $id;
                $requete = $dbConnex->prepare($phraseRequete);
                if( $requete->execute() != true ){ $erreurRequete = true; }
                //pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...
            }
        }
        else{
            // Durée de validité du code expirée

            // c'est bête, ce sont les 5 mêmes lignes de code que juste ci-dessus, mais je n'ai pas trouvé
            // comment 'factoriser' ...
            $_SESSION['client']['mAutor'] = false;

            $erreurRequete = false;
            $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='&#&##&#&', codeDateV='0' WHERE id =" . $id;
            $requete = $dbConnex->prepare($phraseRequete);
            if( $requete->execute() != true ){ $erreurRequete = true; }
            //pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...

            $confirmTestCode =  "<div class='cMessageConfirmation'>" .
                                "<p id='iFocus'>Le code a expiré, mais vous pouvez en demander un nouveau.</p>" .
                                "</div>";
        }
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Suppression des données utilisateur
//
/////////////////////////////////////////////////////////////////////////////////////////////////////
if( isset($_POST['supprimCpte']) ){

    // si la case est cochée, on continue
    if( $_POST['caseSupprimCpte'] == 'on' ){

        // donc maintenant on va supprimer le compte
        // (le début est identique au § sur la validation des données modifiées)
        $phraseRequete = "SELECT id FROM " . TABLE_CLIENTS . " WHERE mail='" . $_SESSION['client']['mail'] . "'";
        $requete = $dbConnex->prepare($phraseRequete);
        $requete->execute();
        $res = $requete->fetch();
        $id = $res['id'];

        // Avant toute chose, si le code d'authentification est périmé, on sort !
        if( (time() < $_SESSION['client']['codeDateV']) ){

            // là il ne reste effectivement plus qu'à supprimer le compte.

            // la vraie suppression immédiate serait :
            // $phraseRequete = "DELETE FROM " . TABLE_CLIENTS . " WHERE id = " . $id;
            $phraseRequete = "UPDATE " . TABLE_CLIENTS .
                             " SET mail='', supprDdee='1', oldMail='" . $_SESSION['client']['mail'] . "', codeModif='&#&##&#&', codeDateV='0'" .
                             " WHERE id =" . $id;
            $requete = $dbConnex->prepare($phraseRequete);
            if( $requete->execute() == true ){

                // la requête a été bien éxécutée, on envoie un mail de confirmation au client
                $objet       = "Confirmation de suppression de compte";
                $rc = "\r\n";
                $messageTxt  =  "Bonjour " . $_SESSION['client']['prenom'] . "," . $rc.$rc .
                                "Votre demande de suppression de compte sur le site " . PHIE_URLC . " a bien été traitée." . $rc.$rc .
                                "Cordialement," . $rc .
                                "Le service technique";
                $messageHtml = $messageTxt; // un jour on fera un joli message HTML !
                mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, $_SESSION['client']['mail'], $objet, $messageTxt, $messageHtml);

                // puis on bifurque sur deconnexion.php
                header('Location: deconnexion.php');
            }
            else{
                $confirmModifs =  "<div class='cMessageConfirmation'>" .
                                  "<p id='iFocus'>Aïe, le serveur est apparemment indisponible.</p>" .
                                  "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
                                  "</div>";
            }
        }
        else{
            // Durée de validité du code expirée

            // c'est bête, ce sont encore les 5 mêmes lignes de code que juste ci-dessus
            $_SESSION['client']['mAutor'] = false;

            $erreurRequete = false;
            $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='&#&##&#&', codeDateV='0' WHERE id =" . $id;
            $requete = $dbConnex->prepare($phraseRequete);
            if( $requete->execute() != true ){ $erreurRequete = true; }
            //pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...

            $confirmTestCode =  "<div class='cMessageConfirmation'>" .
                                "<p id='iFocus'>Le code a expiré, mais vous pouvez en demander un nouveau.</p>" .
                                "</div>";
        }
    }
    else{
        // la case n'a pas été cochée

        $confirmTestCode =  "<div class='cMessageConfirmation'>" .
                            "<p id='iFocus'>La case validant la demande de suppression doit être cochée.</p>" .
                            "</div>";
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Page HTML proprement dite
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

include("inclus/enteteH.php");
?>
<main id='iMain'>

    <section id='iMCDonneesPerso' class='cSectionContour'>
        <h2><?= $_SESSION['client']['civilite'] . "&nbsp;&nbsp;" .
                $_SESSION['client']['prenom']   . "&nbsp;&nbsp;" .
                $_SESSION['client']['nom'] ?>
        </h2>

        <?= isset($confirmEnvoiCode) ? $confirmEnvoiCode : "" ?>
        <?= isset($confirmTestCode)  ? $confirmTestCode  : "" ?>
        <?= isset($confirmModifs)    ? $confirmModifs    : "" ?>

        <form method='POST' class='<?= ($_SESSION['client']['mAutor'] != true) ? "cReadOnly" : "" ?>'>
            <div class='cChampForm'>
                <label for='iMail'>Adresse mail</label>
                <input type='email' id='iMail' name='mail' value='<?= !empty($_POST['mail']) ? $_POST['mail'] : $_SESSION['client']['mail'] ?>'
                                    autofocus placeholder='...' <?= ($_SESSION['client']['mAutor'] != true) ? "readonly" : "" ?> >
                <?php // il ne faut pas mettre 'required' :                                                             ?>
                <?php // - il serait utile pour le bouton 'valider', mais c'est pas grave : on gère le cas en php       ?>
                <?php // - en revanche, il est bloquant pour 'annuler' : on ne peut plus annuler si la case est vide !! ?>
            </div>

            <div class='cChampForm'>
                <label for='iTelMobile'>Téléphone</label>
                <input type='text' id='iTelMobile' name='telMobile' value='<?= !empty($_POST['telMobile']) ? $_POST['telMobile'] : $_SESSION['client']['telMobile'] ?>'
                                    placeholder='-- -- -- -- --' <?= ($_SESSION['client']['mAutor'] != true) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <p class='cLabel'>Mot de passe</p>
                <label for='iAmdp'>ancien</label>
                <input type='password' id='iAmdp' name='amdp' placeholder='...' <?= ($_SESSION['client']['mAutor'] != true) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <label for='iNmdp1'>nouveau</label>
                <input type='password' id='iNmdp1' name='nmdp1' placeholder='...' <?= ($_SESSION['client']['mAutor'] != true) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <label for='iNmdp2'>nouveau (confirmation)</label>
                <input type='password' id='iNmdp2' name='nmdp2' placeholder='...' <?= ($_SESSION['client']['mAutor'] != true) ? "readonly" : "" ?> >
            </div>

            <?php if( $_SESSION['client']['mAutor'] == true ) : ?>
            <div id='iValider'>
                <button class='cDecoBoutonValid' name='validerModifs'>Valider</button>
                <button class='cDecoBoutonAutre' name='annulerModifs'>Annuler</button>
                <?php // NB: en cas d'appui sur ENTER (au clavier), c'est le 1e bouton du 1e form de la page qui est validé.   ?>
                <?php //     (ce n'est pas ce qui m'a guidé pour faire cette page :                                            ?>
                <?php //      1°§ [modif données]    2°§ [dde code]    3°§ [supp données]    n'empêche que c'est bien tombé !) ?>
            </div>
            <?php endif ?>
        </form>
    </section>

    <section id='iMCProcedure' class='cSectionContour'>
        <h2>Données personnelles</h2>
        <p>La modification ou la suppression des données personnelles est soumise à la procédure sécurisée suivante :</p>
        <ol>
            <li>demande d'un code d'authentification, reçu par mail</li>
            <form method='POST'>
                <div>
                    <button class='cDecoBoutonValid' name='demanderCode'>demander un code</button>
                </div>
            </form>
            <li>puis validation de ce code (actif pendant <?= DUREE_VALID_CODE_MODIF ?> mn)</li>
            <form method='POST' id='iMCValiderCode'>
                <div>
                    <label for='iCode'></label>
                    <input type='text' id='iCode' name='code' placeholder='saisir ici le code (reçu par mail)'>
                </div>
                <div>
                    <button class='cDecoBoutonValid' name='validerCode'>Valider</button>
                </div>
            </form>
        </ol>
        <p>Une fois le code validé, les données situées dans le cadre grisé ci-dessus deviennent alors modifiables, et la suppression du compte peut également être demandée ci-dessous.</p>
        <form method='POST' id='iMCSupprimCpte'>
            <div>
                <input type='checkbox' name='caseSupprimCpte'>
                <p>"Je demande expressément la suppression de mon compte et de toutes mes données personnelles attachées au site <?= PHIE_URLC ?>"</p>
            </div>
            <div>
                <?php if( $_SESSION['client']['mAutor'] == true ) : ?>
                <button class='cDecoBoutonValid' name='supprimCpte'>Supprimer mon compte</button>
                <?php endif ?>
            </div>
        </form>
    </section>

</main>

    <?php include("inclus/pdp.php"); ?>

</body>
</html>