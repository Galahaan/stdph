<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>

    <?php // Cette balise sert à dire à Google : "je suis le propriétaire du site, et je souhaite utiliser,        ?>
    <?php // par l'intermédiaire de mon compte Google, les outils de suivi de référencement proposés par Google."  ?>
    <?php // Cette balise DOIT être conservée de façon permanente tant que l'on souhaite utiliser ces outils.      ?>
    <meta name="google-site-verification" content="<?= GOOGLE_VALIDATION_CODE ?>" />

    <?php // Pour un bon positionnement dans les résultats des moteurs de recherche, il est très important ?>
    <?php // de renseigner les balises <title> (max 65 c.) et <meta name='description'> (max 200 c.)       ?>
    <title><?= $pageCourante['titre'] ?></title>
    <meta name='description' content='<?= $enteteSpecs['description'] ?>'>
    <meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= $pageCourante['nom'] ?>, <?= MC_1 ?>, <?= MC_2 ?>, <?= MC_3 ?>'>
    <meta name='robots' content='<?= $enteteSpecs['robots'] ?>'>

    <?php // Prise en compte du responsive design ?>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <?php
    // selon les pages, on peut vouloir un refresh automatique (notamment quand l'heure est affichée !)
    // (mais pas pour Lynx, qui ne le prend pas en compte, et ça gène la navigation)                      ?>
    <?php if( strpos($_SERVER['HTTP_USER_AGENT'], 'ynx') == FALSE ) : ?>
    <?= ! empty($enteteSpecs['refresh']) ? "<" . $enteteSpecs['refresh'] : "" ?>
    <?php endif ?>

    <?php // selon les pages, on peut avoir besoin du CDN de fontAwesome.
          // (on l'appelle en PREMIER, comme ça notre CSS reste prioritaire puisque le fichier HTML est lu de haut en bas) ?>
    <?= ! empty($enteteSpecs['cdn'])     ? "<" . $enteteSpecs['cdn']     : "" ?>

    <link rel='stylesheet' type='text/css' href='css/theme.css'>
    <link rel='stylesheet' type='text/css' href='css/style.css'>
    <link rel='shortcut icon' href='img/icones/favicon.ico'>
</head>

<body <?= $enteteSpecs['focus'] ?> >
    <header>

        <?php // pour Lynx, on enlève le lien vers le piège.                             ?>
        <?php // il n'est pas grave en soi, mais ça fait un lien inutile en haut de page ?>
        <?php if( strpos($_SERVER['HTTP_USER_AGENT'], 'ynx') == FALSE ) : ?>
        <div id='iPiegeAA'><a href='tapette.php'><img src='img/bandeau/tapette.png' alt='tapette à moucherons'></a></div>
        <?php endif ?>

        <nav class='cBraille'><?= $pageCourante['nom'] ?>
            <ol>
                <li><a href='#iMain'       accesskey='c'>[c] contenu de la page <?= $pageCourante['nom'] ?></a></li>
                <li><a href='#iNavigation' accesskey='n'>[n] Menu de navigation</a></li>
                <li><a href='#iLienConnex' accesskey='x'>[x] Connexion/Inscription/Deconnexion</a></li>
                <li><a href='aide.php'     accesskey='h'>[h] Aide à la navigation dans le site</a></li>
            </ol>
        </nav>

        <section>
            <a href='index.php' accesskey='r'>
                <img id='iLogoCroix' src='img/bandeau/croix_caducee.png' alt=''>
                <h1><?= NOM_PHARMA ?></h1>
                <strong><?= LOC_PHARMA ?></strong>
            </a>
            <p id='iTelBandeau'><a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a><img class='cClicIndexTaille' src='img/icones/clicIndex.png' alt=''></p>
        </section>
        <nav id='iNavigation'>
            <ul>
                <li><a <?= ($pageCourante['flag'] == "1000") ? "id = 'iPageCourante'" : "" ?> href='index.php'   >Accueil </a></li>
                <li><a <?= ($pageCourante['flag'] == "0100") ? "id = 'iPageCourante'" : "" ?> href='horaires.php'>Horaires</a></li>
                <li><a <?= ($pageCourante['flag'] == "0010") ? "id = 'iPageCourante'" : "" ?> href='equipe.php'  >Équipe  </a></li>
                <li><a <?= ($pageCourante['flag'] == "0001") ? "id = 'iPageCourante'" : "" ?> href='contact.php' >Contact </a></li>
            </ul>
        </nav>
        <div id='iBandeauConnex'>
            <?php
                if( isset($_SESSION['client']) ){

                    // si le client est connecté, on affiche son nom, le lien vers l'accès à son compte, et le lien pour se déconnecter :
                    echo "<div id='iClientConnecte'>";
                        echo "<a href='mon-compte.php'>" .
                                    $_SESSION['client']['prenom'] . "&nbsp;&nbsp;" .
                                    $_SESSION['client']['nom'] .
                             "</a>";
                    echo "</div>";

                    echo "<div id='iLienConnex'>";
                        echo "<a href='deconnexion.php'>déconnexion</a>";
                    echo "</div>";
                }
                else{

                    // si le client n'est pas connecté, on affiche le lien pour se connecter :
                    // (normalement, pour ordo- et comm-, on ne peut arriver là sans être déjà connecté)
                    echo "<div id='iClientConnecte'>";
                        echo " ";
                    echo "</div>";

                    echo "<div id='iLienConnex'>";
                        echo "<a href='connexion.php'>mon compte</a>";
                    echo "</div>";
                }
            ?>
        </div>
    </header>
