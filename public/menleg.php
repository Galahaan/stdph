<?php

session_start(); // en début de chaque fichier utilisant $_SESSION
ini_set("display_errors", 1);  // affichage des erreurs - à virer à la mise en prod !

require_once("./inclus/constantes.php");
?>

<!DOCTYPE html>
<html lang='fr'>
<head>
    <title><?= NOM_PHARMA . " - Mentions légales"?></title>
    <meta charset='utf-8'>

    <!-- Mots clés de la page -->
    <meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= MC_CP ?>, <?= MC_1 ?>, <?= MC_2 ?>, <?= $pageCourante['nom'] ?>'>

    <!-- Prise en compte du responsive design -->
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel='stylesheet' type='text/css' href='css/style.css'>
    <link rel='shortcut icon' href='img/favicon.ico'>
</head>

<body>
    <header>
        <nav class='cBraille'><?= $pageCourante['nom'] ?>
            <ol>
                <li><a href='aide.php'     accesskey='h'>[h] Aide à la navigation dans le site</a></li>
                <li><a href='#iNavigation' accesskey='n'>[n] Menu de navigation</a></li>
                <li><a href='#iLienConnex' accesskey='c'>[c] Connexion/Inscription/Deconnexion</a></li>
                <li><a href='#iMain'       accesskey='m'>[m] contenu de <?= $pageCourante['nom'] ?></a></li>
            </ol>
        </nav>

        <section>
            <a href='index.php' accesskey='r'>
                <img id='iLogoCroix' src='img/croix_caducee.png' alt=''>
                <h1><?= NOM_PHARMA ?></h1>
                <h2><?= STI_PHARMA ?></h2>
            </a>
            <p id='iTelBandeau'><a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a><img class='cClicIndexTaille' src='img/clicIndex.png' alt=''></p>
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

                    // si le client est connecté, on affiche son nom et le lien pour se déconnecter :
                    echo "<div id='iClientConnecte'>";
                        echo $_SESSION['client']['prenom'] . ' ' . $_SESSION['client']['nom'];
                    echo "</div>";

                    echo "<div id='iLienConnex'>";
                        echo "<a href='deconnexion.php'>déconnexion</a>";
                    echo "</div>";
                }
                else{

                    // si le client n'est pas connecté, on affiche le lien pour se connecter :
                    echo "<div id='iClientConnecte'>";
                        echo " ";
                    echo "</div>";

                    echo "<div id='iLienConnex'>";
                        echo "<a href='connexion.php'>connexion</a>";
                    echo "</div>";
                }
            ?>
        </div>

    </header>

    <main id='iMain'>

        <section id='iMentionsLegales' class='cSectionContour'><h3>Mentions légales</h3>

            <article><h4>Organismes partenaires</h4>
                <p class='gauche'>Agence régionale de santé territorialement compétente</p>
                <p><img src='img/ARS.png' alt=''></p>
                <p class='droite'><?= str_replace(" - ","<br>",ARS_COORD) ?></p>
                <p class='droite'><a href=<?= ARS_URL ?>><?= ARS_URLC ?></a></p>
                <p class='gauche'>Agence nationale de sécurité du médicament et des produits de santé (ANSMPS)</p>
                <p><img src='img/ANSM.png' alt=''></p>
                <p class='droite'><?= str_replace(" - ","<br>",ANSM_COORD) ?></p>
                <p class='droite'><a href=<?= ANSM_URL ?>><?= ANSM_URLC ?></a></p>
                <p class='gauche'>Ordre national des pharmaciens</p>
                <p><img src='img/ONP.png' alt=''></p>
                <p class='droite'><?= str_replace(" - ","<br>",ORDRE_COORD) ?></p>
                <p class='droite'><a href=<?= ORDRE_URL ?>><?= ORDRE_URLC ?></a></p>
                <p class='gauche'>Ministère des solidarités et de la santé</p>
                <p><img src='img/RF.png' alt=''></p>
                <p class='droite'><?= str_replace(" - ","<br>",MINIS_COORD) ?></p>
                <p class='droite'><a href=<?= MINIS_URL ?>><?= MINIS_URLC ?></a></p>
                <p class='gauche'>Commission nationale de l'informatique et des libertés (CNIL)</p>
                <p><img src='img/CNIL.png' alt=''></p>
                <p class='droite'><?= str_replace(" - ","<br>",CNIL_COORD) ?></p>
                <p class='droite'><a href=<?= CNIL_URL ?>><?= CNIL_URLC ?></a></p>
            </article>
            <article><h4>Pharmacie</h4>
                <p class='gauche'>n° de licence</p>
                <p class='droite'><?= PHIE_LICENCE ?></p>
                <p class='gauche'>n° TVA</p>
                <p class='droite'><?= PHIE_TVA ?></p>
                <p class='gauche'>n° SIRET</p>
                <p class='droite'><?= PHIE_SIRET ?></p>
                <p class='gauche'>Code APE</p>
                <p class='droite'><?= PHIE_APE ?></p>
                <p class='gauche'>Hébergeur de <?= PHIE_URLC ?></p>
                <p class='droite'><?= str_replace(" - ","<br>",PHIE_HBG_COORD) ?></p>
                <p class='droite'><a href=<?= PHIE_HBG_URL ?>><?= PHIE_HBG_URLC ?></a></p>
            </article>
            <article><h4>Pharmacien</h4>
                <p class='gauche'>Titulaire</p>
                <p class='droite'><?= PHIEN_TITULAIRE ?></p>
                <p class='gauche'>n° RPPS (inscription à l'Ordre national des pharmaciens)</p>
                <p class='droite'><?= PHIEN_RPPS ?></p>
            </article>

        </section>
    </main>
    <footer>
        <section><h3>Mentions légales</h3>
            <a href="menleg.php">Mentions légales</a>
        </section>
        <section><h3>Informations sur l'editeur du site</h3>
            <p>Édition CLR - 2018</p>
        </section>
    </footer>
</body>
</html>