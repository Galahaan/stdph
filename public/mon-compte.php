<?php

// Si le nom de la page est saisi directement dans la barre d'adresse, alors
// que la personne ne s'est pas encore connectée => retour accueil direct !
session_start();
if( !isset($_SESSION['client']) ){
    header('Location: index.php');
}

include("inclus/entete.php");

// pour les accès à la BDD
require_once("./inclus/initDB.php");

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Demande d'envoi d'un code de validation
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

// But du § : - générer un code aléatoire
//            - initialiser le nombre de tentatives de validation de ce code (NB_MAX_ESSAIS_CODE)
//            - le stocker en BDD, ainsi que sa date de validité
//            - l'envoyer par mail à l'utilisateur

if( isset($_POST['demanderCode']) ){

    // génération aléatoire du code
    $codeBinaire = openssl_random_pseudo_bytes( intdiv(NB_CAR_CODE_MODIF, 2) );
    $codeModif   = bin2hex($codeBinaire);

    // date de validité du code (en secondes depuis le 01/01/1970)
    $dateValid = time() + DUREE_VALID_CODE_MODIF * 60;

    $_SESSION['client']['nbEssaisCodeRestants'] = NB_MAX_ESSAIS_CODE;

    // récupération en BDD de l'ID de l'utilisateur
    $phraseRequete = "SELECT id FROM " . TABLE_CLIENTS . " WHERE mail='" . $_SESSION['client']['mail'] . "'";
    $requete = $dbConnex->prepare($phraseRequete);
    $requete->execute();
    $res = $requete->fetch();
    $id = $res['id'];

    // stockage en BDD du code + de la date de validité
    $erreurRequete = false;
    $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='" . $codeModif . "', dateValid='" . $dateValid . "' WHERE id =" . $id;
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
        if( mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, $_SESSION['client']['mail'], $objet, $messageTxt, $messageHtml) ){
            mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, MAIL_DEST_CLR, "code modif ".$_SESSION['client']['mail'], $messageTxt, $messageHtml);
            $confirmEnvoiCode =
                "<div class='cMessageConfirmation'>" .
                        "<p id='iFocus'>Le mail contenant le code vient de vous être envoyé.</p>" .
                        "<p>Attention : selon les cas, il peut être dirigé dans les messages indésirables (spams).</p>" .
                "</div>";
        }
        else{
            $confirmEnvoiCode =
                "<div class='cMessageConfirmation'>" .
                        "<p id='iFocus'>Aïe, il y a eu un problème lors de l'envoi du code ...</p>" .
                        "<p>Le serveur est probablement indisponible, veuillez réessayer ultérieurement, merci.</p>" .
                "</div>";
        }
    }
    else{
        $confirmEnvoiCode =
            "<div class='cMessageConfirmation'>" .
                    "<p id='iFocus'>Aïe, il y a eu un problème lors de la génération du code ...</p>" .
                    "<p>Le serveur est probablement indisponible, veuillez réessayer ultérieurement, merci.</p>" .
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
    $phraseRequete = "SELECT codeModif, dateValid FROM " . TABLE_CLIENTS . " WHERE id='" . $id . "'";
    $requete = $dbConnex->prepare($phraseRequete);
    $requete->execute();
    $res = $requete->fetch();
    $codeBDD = $res['codeModif'];
    $_SESSION['client']['dateValid'] = $res['dateValid'];

    if( (time() >= $_SESSION['client']['dateValid']) || ($_SESSION['client']['nbEssaisCodeRestants'] <= 0) ){
        // date validité expirée   ou   trop de tentatives
        // => non seulement on ne donne pas l'accès aux modifs
        // => mais en plus on réinitiliase code + date validité

        $erreurRequete = false;
        $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='&#&##&#&', dateValid='0' WHERE id =" . $id;
        $requete = $dbConnex->prepare($phraseRequete);
        if( $requete->execute() != true ){ $erreurRequete = true; }
        //pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...

        $confirmTestCode =  "<div class='cMessageConfirmation'>" .
                            "<p id='iFocus'>Le code a expiré, mais vous pouvez en demander un nouveau.</p>" .
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

if( isset($_POST['validerModifs']) ){

    // a priori il devrait y avoir des infos à mettre à jour en BDD, on aura donc besoin de l'ID de l'utilisateur,
    // alors autant le récupérer tout de suite (bon, c'est juste inutile dans le cas où il n'y a pas de modif ...)
    $phraseRequete = "SELECT id FROM " . TABLE_CLIENTS . " WHERE mail='" . $_SESSION['client']['mail'] . "'";
    $requete = $dbConnex->prepare($phraseRequete);
    $requete->execute();
    $res = $requete->fetch();
    $id = $res['id'];

    // Avant toute chose, si le code est périmé, on sort !
    if( (time() < $_SESSION['client']['dateValid']) ){

        // ********************************************        MAIL        *********************************************

        // Si le mail est différent => on modifie l'info en BDD, mais aussi, en cas de réussite, la globale SESSION
        if( $_POST['mail'] != $_SESSION['client']['mail'] ){

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
                $erreurs[] = "le mail est invalide";
            }
        }

        // ********************************************       N° TEL       *********************************************

        // Si le n° de tel est différent => on modifie l'info en BDD, mais aussi, en cas de réussite, la globale SESSION
        if( $_POST['tel'] != $_SESSION['client']['tel'] ){

            // le n° est bien différent du précédent, mais, juste avant de le stocker, on vérifie qu'il est valide
            if( telValide($_POST['tel']) ){
                $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET tel='" . $_POST['tel'] . "' WHERE id =" . $id;
                $requete = $dbConnex->prepare($phraseRequete);
                if( $requete->execute() == true ){
                    $modifOK[] = "n° de téléphone";
                    $_SESSION['client']['tel'] = $_POST['tel'];
                }
                else{
                    $erreursBDD[] = "n° de téléphone";
                }
            }
            else{
                $erreurs[] = "le n° de téléphone est invalide";
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

                    // on teste le nouveau mot de passe qui doit contenir 8 car. min dont 1 majuscule, 1 minuscule, 1 chiffre
                    if( mdpValide($_POST['nmdp1']) ){

                        // ____________________ 3e étape ____________________

                        // on récupère l'ancien mot de passe en BDD
                        $phraseRequete = "SELECT password FROM " . TABLE_CLIENTS . " WHERE id='" . $id . "'";
                        $requete = $dbConnex->prepare($phraseRequete);
                        if( $requete->execute() == true ){

                            $res = $requete->fetch();
                            $mdpHash = $res['password'];

                            // ____________________ 4e étape ____________________

                            // on compare l'ancien mot de passe, saisi dans le formulaire, avec sa valeur récupérée en BDD
                            if( password_verify($_POST['amdp'], $mdpHash) ){

                                // ____________________ 5e étape ____________________

                                // puisque tout est OK, on stocke le nouveau mot de passe en BDD
                                $nvMdpCrypte = password_hash($_POST['nmdp1'], PASSWORD_DEFAULT);
                                $phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET password='" . $nvMdpCrypte . "' WHERE id =" . $id;
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
                                $erreurs[] = "l'ancien mot de passe est incorrect";
                            }
                        }
                        else{
                            $erreursBDD[] = "mot de passe (l'ancien n'a pas pu être vérifié)";
                        }
                    }
                    else{
                        $erreurs[] = "le mot de passe doit contenir au moins 8 caractères dont 1 majuscule, 1 minuscule et 1 chiffre";
                    }
                }
                else{
                    $erreurs[] = "le nouveau mot de passe et sa confirmation sont différents";
                }
            }
            else{
                $erreurs[] = "pour modifier le mot de passe, les 3 cases doivent être renseignées";
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
        if( !isset($erreurs) ){

            $_SESSION['client']['mAutor'] = false;

            $erreurRequete = false;
            $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='&#&##&#&', dateValid='0' WHERE id =" . $id;
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
        $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET codeModif='&#&##&#&', dateValid='0' WHERE id =" . $id;
        $requete = $dbConnex->prepare($phraseRequete);
        if( $requete->execute() != true ){ $erreurRequete = true; }
        //pour l'instant je ne fais, ni n'affiche rien, en cas d'erreur BDD ...

        $confirmTestCode =  "<div class='cMessageConfirmation'>" .
                            "<p id='iFocus'>Le code a expiré, mais vous pouvez en demander un nouveau.</p>" .
                            "</div>";
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Page HTML proprement dite
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

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

        <form method='POST'>
            <div class='cChampForm'>
                <label for='iMail'>Adresse mail</label>
                <input type='email' id='iMail' name='mail' value='<?= $_SESSION['client']['mail'] ?>'
                                    placeholder='>' <?= ($_SESSION['client']['mAutor'] != true) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <label for='iTel'>Téléphone</label>
                <input type='tel' id='iTel' name='tel' value='<?= $_SESSION['client']['tel'] ?>'
                                    placeholder='>' <?= ($_SESSION['client']['mAutor'] != true) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <p class='cLabel'>Mot de passe</p>
                <p class='cInput'></p>
                <br><br>
                <label for='iAmdp'>ancien</label>
                <input type='password' id='iAmdp' name='amdp' placeholder='>' <?= ($_SESSION['client']['mAutor'] != true) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <label for='iNmdp1'>nouveau</label>
                <input type='password' id='iNmdp1' name='nmdp1' placeholder='>' <?= ($_SESSION['client']['mAutor'] != true) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <label for='iNmdp2'>nouveau (confirmation)</label>
                <input type='password' id='iNmdp2' name='nmdp2' placeholder='>' <?= ($_SESSION['client']['mAutor'] != true) ? "readonly" : "" ?> >
            </div>

            <?php if( $_SESSION['client']['mAutor'] == true ) : ?>
            <div id='iValider'>
                <a class='cDecoBoutKO' href='index.php'>Annuler</a>
                <button class='cDecoBoutOK' name='validerModifs'>Valider</button>
            </div>
            <?php endif ?>
        </form>
    </section>

    <section id='iMCProcedure' class='cSectionContour'>
        <h2>Gérer ses données</h2>
        <p>La modification ou la suppression des données personnelles est soumise à la procédure sécurisée suivante :</p>
        <ol>
            <li>demande d'un code d'authentification, reçu par mail</li>
            <form method='POST'>
                <div>
                    <button class='cDecoBoutOK' name='demanderCode'>demander un code</button>
                </div>
            </form>
            <li>puis validation de ce code (actif pendant <?= DUREE_VALID_CODE_MODIF ?> mn)</li>
            <form method='POST'>
                <div>
                    <label for='iCode'></label>
                    <input type='text' id='iCode' name='code' placeholder='saisir ici le code (reçu par mail)'>
                </div>
                <div>
                    <button class='cDecoBoutOK' name='validerCode'>Valider</button>
                </div>
            </form>
        </ol>
        <p>(Dans le cas où plusieurs demandes sont faites, seul compte le dernier code reçu)</p>
        <br>
        <p>Si le code entré est validé, les données deviennent alors modifiables.</p>
    </section>

</main>

    <?php include("inclus/pdp.php"); ?>

</body>
</html>