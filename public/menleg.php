<?php

include('inclus/entete.php');

?>
    <main id='iMain'>

        <section id='iMentionsLegales' class='cSectionContour'><h3>Mentions légales</h3>

            <article><h4>Organismes partenaires</h4>
                <div>
                    <p class='gauche'>Agence régionale de santé territorialement compétente</p>
                    <p><img src='img/menleg/ARS.png' alt=''></p>
                    <p class='droite'><?= str_replace(" - ","<br>",ARS_COORD) ?></p>
                    <p class='droite'><a href=<?= ARS_URL ?>><?= ARS_URLC ?></a></p>
                </div>
                <div>
                    <p class='gauche'>Agence nationale de sécurité du médicament et des produits de santé (ANSMPS)</p>
                    <p><img src='img/menleg/ANSM.png' alt=''></p>
                    <p class='droite'><?= str_replace(" - ","<br>",ANSM_COORD) ?></p>
                    <p class='droite'><a href=<?= ANSM_URL ?>><?= ANSM_URLC ?></a></p>
                </div>
                <div>
                    <p class='gauche'>Ordre national des pharmaciens</p>
                    <p><img src='img/menleg/ONP.png' alt=''></p>
                    <p class='droite'><?= str_replace(" - ","<br>",ORDRE_COORD) ?></p>
                    <p class='droite'><a href=<?= ORDRE_URL ?>><?= ORDRE_URLC ?></a></p>
                </div>
                <div>
                    <p class='gauche'>Ministère des solidarités et de la santé</p>
                    <p><img src='img/menleg/RF.png' alt=''></p>
                    <p class='droite'><?= str_replace(" - ","<br>",MINIS_COORD) ?></p>
                    <p class='droite'><a href=<?= MINIS_URL ?>><?= MINIS_URLC ?></a></p>
                </div>
                <div>
                    <p class='gauche'>Commission nationale de l'informatique et des libertés (CNIL)</p>
                    <p><img src='img/menleg/CNIL.png' alt=''></p>
                    <p class='droite'><?= str_replace(" - ","<br>",CNIL_COORD) ?></p>
                    <p class='droite'><a href=<?= CNIL_URL ?>><?= CNIL_URLC ?></a></p>
                </div>
            </article>
            <article><h4>Pharmacie</h4>
                <div>
                    <p class='gauche'>n° de licence</p>
                    <p class='droite'><?= PHIE_LICENCE ?></p>
                </div>
                <div>
                    <p class='gauche'>n° TVA</p>
                    <p class='droite'><?= PHIE_TVA ?></p>
                </div>
                <div>
                    <p class='gauche'>n° SIRET</p>
                    <p class='droite'><?= PHIE_SIRET ?></p>
                </div>
                <div>
                    <p class='gauche'>Code APE</p>
                    <p class='droite'><?= PHIE_APE ?></p>
                </div>
                <div>
                    <p class='gauche'>Hébergeur de <?= PHIE_URLC ?></p>
                    <p class='droite'><?= str_replace(" - ","<br>",PHIE_HBG_COORD) ?></p>
                    <p class='droite'><a href=<?= PHIE_HBG_URL ?>><?= PHIE_HBG_URLC ?></a></p>
                </div>
            </article>
            <article><h4>Pharmacien</h4>
                <div>
                    <p class='gauche'>Titulaire</p>
                    <p class='droite'><?= PHIEN_TITULAIRE ?></p>
                </div>
                <div>
                    <p class='gauche'>n° RPPS (inscription à l'Ordre national des pharmaciens)</p>
                    <p class='droite'><?= PHIEN_RPPS ?></p>
                </div>
            </article>

        </section>
    </main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>