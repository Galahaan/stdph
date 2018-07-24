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
// - si le mail est bien dans la BDD, on l'utilise pour lui envoyer un lien de réinitialisation,
//   tout en conservant ce mail dans la $_SESSION !!
//   C'est très important, sinon on ne pourra pas identifier l'utilisateur au moment où il voudra soumettre
//   son code de réinitialisation.
//   Cela veut aussi dire que l'utilisateur ne doit pas s'interrompre au cours de la procédure
//   (durée max = durée de la $_SESSION)
//
// - lorsque l'utilisateur clique sur le lien qu'on lui a fourni dans le mail, cela déclenche l'ouverture
//   de cette même page avec, dans l'URL, le code de réinitialisation.
//
// - si le code correspond bien à celui stocké en BDD, on affiche alors un nouveau formulaire dans lequel
//   le client doit saisir et confirmer son nouveau mdp
//
// On n'oublie pas non plus d'intercaler un test, pour être sûr qu'au moment où l'on souhaite utiliser
// la $_SESSION, ie en cliquant sur le lien du mail, il n'y a pas déjà une session d'un (autre) user en cours.
// Si c'est le cas, on arrête la procédure de 'mot de passe oublié' et on remet les variables à 0.
//
//
//
// Variante :
//
// On aurait aussi pu, lors de l'envoi du mail au user étourdi, joindre un code et un lien vers cette
// même page, avec une variable en paramètre GET, pour déclencher l'affichage d'un formulaire dans lequel
// il aurait fallu saisir le mail + le code
// => en cas de match, on ouvrait le formulaire avec les 2 mdp
// Ca évite d'utiliser la $_SESSION, mais c'est plus long pour le user
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
                // - stocker le mail en $_SESSION de façon à identifier l'utilisateur au moment du test de son code

                // génération aléatoire du code
                $codeGene = genCode();

                // cryptage
                $codeCrypte = password_hash($codeGene, PASSWORD_DEFAULT);

                // stockage en BDD
                $phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET rst='" . $codeCrypte . "' WHERE id=" . $id;
                $requete = $dbConnex->prepare($phraseRequete);

                // si la requête s'est bien passée, on prévient l'utilisateur par mail
                if( $requete->execute() == true ){

                    // => on envoie un mail de confirmation au client
                    $objet       = "procédure \"mot de passe oublié\"";
                    $rc = "\r\n";
                    $messageTxt  =  "Bonjour " . $prenom . "," . $rc.$rc .
                                    "Suite à votre demande sur " . PHIE_URLC . " , vous pouvez maintenant définir un nouveau mot de passe " . $rc .
                                    "en cliquant sur le lien ci-dessous :" . $rc.$rc .
                                    "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    "<a href='" . SW_ADRESSE_SITE_PHARMACIE . "reinitMdp.php?rst=" . $codeCrypte . "'>" .
                                    "- définir un nouveau mot de passe -" .
                                    "</a>". $rc.$rc.$rc .
                                    "Si le lien ne fonctionne pas, copiez-collez la ligne ci-dessous dans votre navigateur :" . $rc .
                                    SW_ADRESSE_SITE_PHARMACIE . "reinitMdp.php?rst=" . $codeCrypte . $rc.$rc .
                                    "Cordialement," . $rc .
                                    "Le service technique";
                    $messageHtml = $messageTxt; // un jour on fera un joli message HTML !
                    if( mailTxHt(PHIE_URLC, ADR_EXP_HBG, ADR_MAIL_PHARMA, $mail, $objet, $messageTxt, $messageHtml) ){

                        // en tout dernier point, comme tout s'est bien passé, on sait que l'utilisateur
                        // va revenir soumettre le code qu'il aura reçu par mail ...
                        // on aura donc besoin de l'identifier, ce que l'on fera grâce à la $_SESSION :
                        $_SESSION['mailProcMdp'] = $mail;

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
                $erreur = "Désolé, ce mail est inconnu.";
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


/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Test du code de réinitialisation
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

// on passe par ici dès que l'utilisateur a suivi le lien (contenant le code 'rst') qu'on lui a envoyé par mail.
// (et même après validation du formulaire 'validerMdp', contenant le mdp et sa confirm, on repasse par ici)
if( isset($_GET['rst']) && !empty($_GET['rst']) ){

    // le but est d'identifier l'utilisateur, de façon à lui proposer de saisir son nouveau mdp,
    // donc, par défaut, on initialise le résultat à false :
    $userOk = false;

    // si, en arrivant en provenance du lien du mail, on interfère dans une session en cours d'un autre user,
    // on est stoppé :
    if( ! isset($_SESSION['client']) ){

        // on récupère le code envoyé dans l'URL :
        if( strlen(strip_tags($_GET['rst'])) == strlen($_GET['rst']) ){

            $codeUser = strip_tags($_GET['rst']);

            // on le compare à celui stocké en BDD, grâce à la variable de session qui contient le mail du user !
            // et si jamais la session a expiré, pour éviter une erreur SQL, on teste la validité du mail
            if( mailValide($_SESSION['mailProcMdp']) ){

                $phraseRequete = 'SELECT rst FROM ' . TABLE_CLIENTS . " WHERE mail='" . $_SESSION['mailProcMdp'] . "'";
                $requete = $dbConnex->prepare($phraseRequete);
                if( $requete->execute() ){

                    $res = $requete->fetch();
                    if( $codeUser == $res['rst'] ){

                        // c'est le bon user :
                        $userOk = true;
                    }
                    else{
                        // les codes sont différents
                        // - soit il y a tentative de piratage
                        // - soit, plus probablement, la procédure a été déclenchée plusieurs fois et on a utilisé un mail précédent
                        $confirmation = "<div class='cMessageConfirmation'>" .
                                            "<p id='iFocus'>Le code est invalide ou expiré ... (il faut utiliser le lien du dernier mail reçu)</p>" .
                                        "</div>";
                    }
                }
                else{
                    // le code 'rst' n'a pas pu être lu en BDD
                    $confirmation = "<div class='cMessageConfirmation'>" .
                                        "<p id='iFocus'>Aïe, le serveur est apparemment indisponible.</p>" .
                                        "<p>Veuillez nous en excuser et réessayer ultérieurement.</p>" .
                                        "<p>(code inconnu)</p>" .
                                    "</div>";
                }
            }
            else{
                // a priori la session a expiré, mais on fait un message plus évasif :
                $confirmation = "<div class='cMessageConfirmation'>" .
                                    "<p id='iFocus'>Le code a expiré ... veuillez renouveler la procédure.</p>" .
                                "</div>";
            }
        }
        else{
            // le code est erroné, on ne va pas plus loin
            $confirmation = "<div class='cMessageConfirmation'>" .
                                "<p id='iFocus'>Erreur d'identification ... veuillez renouveler la procédure.</p>" .
                            "</div>";
        }
    }
    else{
        // une session est déjà en cours
        $confirmation = "<div class='cMessageConfirmation'>" .
                            "<p id='iFocus'>" . $_SESSION['client']['prenom'] . " a actuellement une session en cours. La procédure ne peut aboutir, il faudra la renouveler ...</p>" .
                        "</div>";
        // dans un souci de sécurité, on supprime le code en BDD, et on termine la session
        $phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET rst='' WHERE mail='" . $_SESSION['mailProcMdp'] . "'";
        $requete = $dbConnex->prepare($phraseRequete);
        $requete->execute();
        // je ne fais pas de test de succès, parce qu'au pire, c'est pas très grave

        // enfin, je termine la session :
        unset($_SESSION['mailProcMdp']);
    }
}


/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Traitement du formulaire contenant le nouveau mdp et sa confirm
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

if( isset($_POST['validerMdp']) && !empty($_POST['mdp']) && !empty($_POST['mdpc']) && $userOk == true ){

    if( mdpValide($_POST['mdp']) && mdpValide($_POST['mdpc']) ){
        $mdp  = $_POST['mdp'];
        $mdpc = $_POST['mdpc'];

        if( $mdp == $mdpc ){

            // on crypte le nouveau mdp avant de le stocker en BDD
            $mdpCrypte = password_hash($mdp, PASSWORD_DEFAULT);

            // on le stocke
            $phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET pwd='" . $mdpCrypte . "' WHERE mail='" . $_SESSION['mailProcMdp'] . "'";
            $requete = $dbConnex->prepare($phraseRequete);
            if( $requete->execute() ){
                $confirmation = "<div class='cMessageConfirmation'>" .
                                    "<p id='iFocus'>Votre nouveau mot de passe a bien été enregistré.</p>" .
                                "</div>";

                // dans un souci de sécurité, on supprime le code en BDD, et on termine la session
                $phraseRequete = 'UPDATE ' . TABLE_CLIENTS . " SET rst='' WHERE mail='" . $_SESSION['mailProcMdp'] . "'";
                $requete = $dbConnex->prepare($phraseRequete);
                $requete->execute();
                // je ne fais pas de test de succès, parce qu'au pire, c'est pas très grave

                // enfin, je termine la session :
                unset($_SESSION['mailProcMdp']);
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
            $erreur = "Les 2 mots de passe sont différents.";
        }
    }
    else{
        $erreur = "Mot de passe invalide (de " . NB_CAR_MIN_MDP . " à " . NB_CAR_MAX_MDP . " car. dont 1 chiffre, 1 majuscule, 1 minuscule)";
    }
}

include('inclus/enteteH.php');
?>
	<main id='iMain'>
        <section id='iRMdP' class='cSectionContour'><h2>Mot de passe oublié</h2>

            <?php if( isset($confirmation) ) : // ce sont tous les cas où l'on a validé l'un ou l'autre des 2 formulaires ?>

                <?= $confirmation; ?>

            <?php elseif( ! isset($_GET['rst']) ) : // ce sont tous les cas où l'on arrive sur la page sans venir du lien du mail ?>

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

            <?php elseif( $userOk == true ) : // c'est l'unique cas où l'on vient du mail et que le code est valide ?>

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

            <?php endif ?>

        </section>
	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>