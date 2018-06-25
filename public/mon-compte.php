<?php

// Si le nom de la page est saisi directement dans la barre d'adresse, alors
// que la personne ne s'est pas encore connectée => retour accueil direct !
session_start();
if( !isset($_SESSION['client']) ){
    header('Location: index.php');
}

include("inclus/entete.php");

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Demande du code de validation
//
/////////////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Test du code de validation
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

if(true){
    // récupération en BDD du code, et de sa durée de validité

    // si validité expirée, on ne donne pas l'accès aux modifs
        $modifsAutorisees = false;
    // sinon

        // comparaison entre le code BDD et le code saisi
            // !=
            $modifsAutorisees = false;
            // ==
            //$modifsAutorisees = true;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Modification des données utilisateur
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

// ici on est obligé d'utiliser la fonction native telle quelle, sinon elle ne peut pas jouer son rôle de "_once" :
require_once("./inclus/initDB.php");

if( isset($_POST['validerModifs']) ){

    // a priori il devrait y avoir des infos à mettre à jour en BDD, on aura donc besoin de l'ID de l'utilisateur,
    // alors autant le récupérer tout de suite (bon, c'est juste inutile dans le cas où il n'y a pas de modif ...)
    $phraseRequete = "SELECT id FROM " . TABLE_CLIENTS . " WHERE mail='" . $_SESSION['client']['mail'] . "'";
    $requete = $dbConnex->prepare($phraseRequete);
    $requete->execute();
    $res = $requete->fetchAll();
    $id = $res[0]['id'];

    if( $_POST['mail'] != $_SESSION['client']['mail'] ){

        // le mail est différent
        //    => on va donc modifier l'info en BDD, mais aussi, en cas de réussite, la globale SESSION

        $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET mail='" . $_POST['mail'] . "' WHERE id =" . $id;
        $requete = $dbConnex->prepare($phraseRequete);
        if( $requete->execute() == true ){
            $modif[] = "mail";
            $_SESSION['client']['mail'] = $_POST['mail'];
        }
        else{
            $erreur[] = "mail";
        }
    }
    if( $_POST['tel'] != $_SESSION['client']['tel'] ){

        // le n° de tel est différent
        //    => on va donc modifier l'info en BDD, mais aussi, en cas de réussite, la globale SESSION

        $phraseRequete = "UPDATE " . TABLE_CLIENTS . " SET tel ='" . $_POST['tel'] . "' WHERE id =" . $id;
        $requete = $dbConnex->prepare($phraseRequete);
        if( $requete->execute() == true ){
            $modif[] = "n° de téléphone";
            $_SESSION['client']['tel'] = $_POST['tel'];
        }
        else{
            $erreur[] = "n° de tel";
        }
    }
    //   faire le § sur le mot de passe :

    //   - vérifier que l'ancien mot de passe est correct
    //   - vérifier que les 2 nouveaux mots de passe sont identiques
    //   - stocker le nouveau mot de passe
    //   - faire une fonction de test d'un mot de passe : 1 majuscule, 1 minuscule, 1 chiffre, 8 car. min
}
?>

<main id='iMain'>

<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Messages de confirmation / erreur suite à la modification des données
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

if( isset($_POST['validerModifs']) ){
    if( isset($modif) ){
        if( sizeof($modif) > 1 ){
            echo "Les éléments suivants ont bien été mis à jour :<br>";
            for( $i = 0; $i < sizeof($modif); $i++){
                echo "- " . $modif[$i] . "<br>";
            }
        }
        else{
            echo "Votre " . $modif[0] . " a bien été mis à jour<br>";
        }
    }
    // ça ne devrait pas arriver, mais au cas où 
    if( isset($erreur) ){
        if( sizeof($erreur) > 1 ){
            echo "Problème serveur : échec de la mise à jour concernant les éléments suivants :<br>";
            for( $i = 0; $i < sizeof($erreur); $i++){
                echo "- " . $erreur[$i] . "<br>";
            }
            echo "Veuillez nous en excuser et réessayer ultérieurement.";
        }
        else{
            echo "Problème serveur : le " . $erreur[0] . " n'a pas été mis à jour<br>";
            echo "Veuillez nous en excuser et réessayer ultérieurement.";
        }
    }
    echo "<style section display none>";
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//             Page HTML proprement dite
//
/////////////////////////////////////////////////////////////////////////////////////////////////////

?>
    <section id='iMCDonneesPerso' class='cSectionContour'>
        <h2><?= $_SESSION['client']['civilite'] . "&nbsp;&nbsp;" .
                $_SESSION['client']['prenom']   . "&nbsp;&nbsp;" .
                $_SESSION['client']['nom'] ?>
        </h2>
        <form method='POST'>
            <div class='cChampForm'>
                <label for='iMail'>Adresse mail</label>
                <input type='email' id='iMail' name='mail' value='<?= $_SESSION['client']['mail'] ?>'
                                    placeholder='>' <?= ($modifsAutorisees == false) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <label for='iTel'>Téléphone</label>
                <input type='tel' id='iTel' name='tel' value='<?= $_SESSION['client']['tel'] ?>'
                                    placeholder='>' <?= ($modifsAutorisees == false) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <p class='cLabel'>Mot de passe</p>
                <p class='cInput'></p>
                <br><br>
                <label for='iAmdp'>ancien</label>
                <input type='password' id='iAmdp' name='amdp' placeholder='>' <?= ($modifsAutorisees == false) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <label for='iNmdp1'>nouveau</label>
                <input type='password' id='iNmdp1' name='nmdp1' placeholder='>' <?= ($modifsAutorisees == false) ? "readonly" : "" ?> >
            </div>

            <div class='cChampForm'>
                <label for='iNmdp2'>nouveau (confirmation)</label>
                <input type='password' id='iNmdp2' name='nmdp2' placeholder='>' <?= ($modifsAutorisees == false) ? "readonly" : "" ?> >
            </div>

            <div id='iValider'>
                <a class='cDecoBoutKO' href='index.php'>Annuler</a>
                <button class='cDecoBoutOK' name='validerModifs'>Valider</button>
            </div>
        </form>
    </section>

    <section id='iMCProcedure' class='cSectionContour'>
        <h2>Gérer ses données</h2>
        <p>La modification ou la suppression des données personnelles est soumise à la procédure sécurisée suivante :</p>
        <ol>
            <li>demande d'un code d'authentification, reçu par mail</li>
            <form method='POST'>
                <div>
                    <button class='cDecoBoutOK' name='demanderCode'>demande de code</button>
                </div>
            </form>
            <li>puis validation de ce code, valable pendant 5 mn seulement</li>
            <form method='POST'>
                <div>
                    <label for='iCode'></label>
                    <input type='text' id='iCode' name='code' placeholder='code de 25 caractères ...'>
                </div>
                <div>
                    <button class='cDecoBoutOK' name='validerCode'>Valider</button>
                </div>
            </form>
        </ol>
        <p>(si plusieurs codes sont demandés, seul le dernier est valable)</p>
        <br>
        <p>Les données deviennent alors modifiables.</p>
    </section>
</main>

    <?php include("inclus/pdp.php"); ?>

</body>
</html>