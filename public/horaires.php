<?php

include('inclus/entete.php');

?>
	<main id='iMain'>
		<?php
			$aujourdhui = dateFr();				// fonction qui génère une date de la forme : vendredi 2 juillet 2017
			$auj = substr($aujourdhui, 0, 3);	// on garde les 3 1ères lettres de la chaîne

			$heure  = heureActuelle("");		// heure au format "décimal"
			$heureH = heureActuelle("H");		// heure au format "horaire", ie non décimal !

			// getDeltaP( $heure ) retourne la valeur en % dont il faut décaler (left: ) la div représentant le trait vertical
			// (en fonction de l'heure de la journée) mais également l'information s'il faut ou non afficher le trait.
			//
			// ATTENTION : ceci implique de ne RIEN changer aux valeurs de width et de padding left ou right du § CSS intitulé
			//							"largeurs et marges des jours et des créneaux horaires"

			$resultat		= getDeltaP($heure);
			$deltaP			= $resultat[0];
			$dessinerTrait	= $resultat[1];

			// passé 12h30, on "désactive" le créneau du matin, et passé 19h30, on "désactive" le créneau de l'après-midi,
			// pour le samedi, passé 16h, on désactive les 2 <div>,
			// ie qu'on les remet avec la couleur de fond, un peu plus pâle, des autres jours :
			$matinOff  = ( $heure >= FMATD ) ? true : false;
			$apremOff  = ( $heure >= FAMID ) ? true : false;
			$samediOff = ( $heure >= SA_FAMID ) ? true : false;

			// ouverturePharmacie() génère un message sur l'état d'ouverture ou de fermeture de la pharmacie (ou de leur proximité)
		?>

		<section id='iHorairesIntro' class='cSectionContour'><h2><?= $aujourdhui . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class='cHeureDuJour'>". $heureH . "</span>" ?></h2>
			<p><?= ouverturePharmacie( $auj, $heure ) ?></p>
			<p class='cBraille'><?= HORAIRES_PHARMACIE ?></p>
		</section>
		<?php // Pour cacher ce rontudju de tableau aux lecteurs d'écrans, c'est pas simple !!                     ?>
		<?php // - 1     - vérifier si le display:none du CSS fonctionne sur Jaws ...                              ?>
		<?php //           (en enlevant le aria-hidden ci-dessous, sinon on ne saura pas lequel a un effet)        ?>
		<?php // - 1 bis - si le display:none ne fonctionne pas, essayer le aria-hidden ci-dessous                 ?>
		<?php // - 2     - spécifiquement pour Lynx, comme les 2 cas ci-dessus ne fonctionnent pas,                ?>
		<?php //            si on détecte ce navigateur, on masque le tableau                                      ?>

		<?php if( strpos($_SERVER['HTTP_USER_AGENT'], 'ynx') == FALSE ) : ?>

		<section id='iHorairesTableau' class='cSectionContour' aria-hidden='true'><h2>Horaires d'ouverture de la <?= NOM_PHARMA ?></h2>
			<article class='cSemaine' <?= ($auj == "lun") ? "id='iAujourdhui'" : "" ?>  >
				<p class='cJour'>lundi</p><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >8h30</div><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >12h30</div><div class='cTiret'>-</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >14h</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >19h30</div>

				<?php // comme cette dernière <div> est en position absolute, c'est pas grave si on laisse
				      // de la place dans l'éditeur après la <div> précédente : l'espace ne se verra pas en HTML ?>

				<div <?= ($auj == "lun" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
			<article class='cSemaine' <?= ($auj == "mar") ? "id='iAujourdhui'" : "" ?>  >
				<p class='cJour'>mardi</p><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >8h30</div><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >12h30</div><div class='cTiret'>-</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >14h</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >19h30</div>

				<div <?= ($auj == "mar" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
			<article class='cSemaine' <?= ($auj == "mer") ? "id='iAujourdhui'" : "" ?>  >
				<p class='cJour'>mercredi</p><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >8h30</div><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >12h30</div><div class='cTiret'>-</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >14h</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >19h30</div>

				<div <?= ($auj == "mer" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
			<article class='cSemaine' <?= ($auj == "jeu") ? "id='iAujourdhui'" : "" ?>  >
				<p class='cJour'>jeudi</p><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >8h30</div><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >12h30</div><div class='cTiret'>-</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >14h</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >19h30</div>

				<div <?= ($auj == "jeu" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
			<article class='cSemaine' <?= ($auj == "ven") ? "id='iAujourdhui'" : "" ?>  >
				<p class='cJour'>vendredi</p><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >8h30</div><div <?= ($matinOff) ? "class='cCreneauOff'" : "" ?> >12h30</div><div class='cTiret'>-</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >14h</div><div <?= ($apremOff) ? "class='cCreneauOff'" : "" ?> >19h30</div>

				<div <?= ($auj == "ven" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
			<article class='cSamedi' <?= ($auj == "sam") ? "id='iAujourdhui'" : "" ?>  >
				<p class='cJour'>samedi</p><div <?= ($samediOff) ? "class='cCreneauOff'" : "" ?> >9h</div><div <?= ($samediOff) ? "class='cCreneauOff'" : "" ?> >16h</div>

				<div <?= ($auj == "sam" && $dessinerTrait == true) ? "id='iTraitHoraire'" : "class='cEffacerTrait'" ?> style='left:<?= $deltaP ?>%'>&nbsp;</div>

			</article>
		</section>

		<?php endif ?>

	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>