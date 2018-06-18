<?php

include('inclus/entete.php');

?>
    <main id='iMain'>

        <section id='iMenLeg' class='cSectionContour'><h2>Mentions légales</h2>

            <section id='iMenLegOP'><h3>Organismes partenaires</h3>
                <article>
                    <h4>Agence régionale de santé territorialement compétente</h4>
                    <p><img src='img/menleg/ARS.png' alt=''></p>
                    <p class='cDroite'><?= str_replace(" - ","<br>",ARS_COORD) ?></p>
                    <p class='cDroite'><a href=<?= ARS_URL ?>><?= ARS_URLC ?></a></p>
                    <p></p>
                <article>
                    <h4>Agence nationale de sécurité du médicament et des produits de santé (ANSMPS)</h4>
                    <p><img src='img/menleg/ANSM.png' alt=''></p>
                    <p class='cDroite'><?= str_replace(" - ","<br>",ANSM_COORD) ?></p>
                    <p class='cDroite'><a href=<?= ANSM_URL ?>><?= ANSM_URLC ?></a></p>
                    <p></p>
                </article>
                <article>
                    <h4>Ordre national des pharmaciens</h4>
                    <p><img src='img/menleg/ONP.png' alt=''></p>
                    <p class='cDroite'><?= str_replace(" - ","<br>",ORDRE_COORD) ?></p>
                    <p class='cDroite'><a href=<?= ORDRE_URL ?>><?= ORDRE_URLC ?></a></p>
                    <p></p>
                </article>
                <article>
                    <h4>Ministère des solidarités et de la santé</h4>
                    <p><img src='img/menleg/RF.png' alt=''></p>
                    <p class='cDroite'><?= str_replace(" - ","<br>",MINIS_COORD) ?></p>
                    <p class='cDroite'><a href=<?= MINIS_URL ?>><?= MINIS_URLC ?></a></p>
                    <p></p>
                </article>
                <article>
                    <h4>Commission nationale de l'informatique et des libertés (CNIL)</h4>
                    <p><img src='img/menleg/CNIL.png' alt=''></p>
                    <p class='cDroite'><?= str_replace(" - ","<br>",CNIL_COORD) ?></p>
                    <p class='cDroite'><a href=<?= CNIL_URL ?>><?= CNIL_URLC ?></a></p>
                    <p>En respect de la réglementation, vous pouvez à tout moment demander la modification ou la suppression de vos données personnelles, par mail ou par courrier, à la <?= NOM_PHARMA ?> dont vous trouverez les coordonnées à la rubrique 'Contact' du site.</p>
                </article>
            </section>
            <section><h3>Pharmacie</h3>
                <article>
                    <h4>n° de licence</h4>
                    <p class='cDroite'><?= PHIE_LICENCE ?></p>
                </article>
                <article>
                    <h4>n° TVA</h4>
                    <p class='cDroite'><?= PHIE_TVA ?></p>
                </article>
                <article>
                    <h4>n° SIRET</h4>
                    <p class='cDroite'><?= PHIE_SIRET ?></p>
                </article>
                <article>
                    <h4>Code APE</h4>
                    <p class='cDroite'><?= PHIE_APE ?></p>
                </article>
                <article>
                    <h4>Hébergeur de <?= PHIE_URLC ?></h4>
                    <p class='cDroite'><?= str_replace(" - ","<br>",PHIE_HBG_COORD) ?></p>
                    <p class='cDroite'><a href=<?= PHIE_HBG_URL ?>><?= PHIE_HBG_URLC ?></a></p>
                </article>
            </section>
            <section><h3>Pharmacien</h3>
                <article>
                    <h4>Titulaire</h4>
                    <p class='cDroite'><?= PHIEN_TITULAIRE ?></p>
                </article>
                <article>
                    <h4>n° RPPS (inscription à l'Ordre national des pharmaciens)</h4>
                    <p class='cDroite'><?= PHIEN_RPPS ?></p>
                </article>
            </section>

        </section>
    </main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>