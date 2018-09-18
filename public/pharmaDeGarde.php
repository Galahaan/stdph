<?php

include('inclus/enteteP.php');

include("inclus/enteteH.php");
?>
	<main id='iMain'>
		<?php $heure = heureActuelle(''); ?>

		<section id='iPdG3237' class='cSectionContour'>

		<?php // si les gardes fonctionnent sans passer par le commissariat, ou si on est dans la journée : ?>
		<?php if( (HEURE_SOIR_POLICE_D == "X") || ((HEURE_MATIN_POLICE_D <= $heure) && ($heure < HEURE_SOIR_POLICE_D)) ) : ?>
			<p  class='cInLi'>Trouvez la&nbsp;</p>
			<h2 class='cInLi' id='iPdGh21'>pharmacie de garde</h2>
			<p  class='cInLi'>&nbsp;la plus proche de chez vous en cliquant sur la croix ci-dessous :</p>
			<p id='iPdGcroix'>
				<a href='http://www.3237.fr/'>
					<img src='img/icones/croix_garde.png' alt=''>
					<span class='cBraille'>croix</span>
				</a>
			</p>
		<?php else : // on est en horaires de garde -> on affiche juste le titre ?>
			<h2 id='iPdGh22'>Pharmacie de garde</h2>
		<?php endif ?>

		</section>

		<section id='iPdGplan' class='cSectionContour'>

			<?php // pour Lynx, on enlève le h2 ?>
			<?php if( strpos($_SERVER['HTTP_USER_AGENT'], 'ynx') == FALSE ) : ?>
			<h2>Localiser le commissariat de police</h2>
			<?php endif ?>

		<?php // si les gardes fonctionnent sans passer par le commissariat, il n'y a RIEN d'autre à afficher ?>
		<?php if( HEURE_SOIR_POLICE_D != "X" ) : ?>
			<?php // quelle que soit l'heure, on informe les gens du fonctionnement en horaires de garde, et on propose le plan : ?>
			<p>À partir de <span><?= HEURE_SOIR_POLICE_H ?></span>, et jusqu'à <span><?= HEURE_MATIN_POLICE_H ?></span> le lendemain matin, il faut se rendre, avec une pièce d'<span>identité</span> et une <span>ordonnance</span>, au <span>commissariat de police</span> situé :</p>
			<p><?= ADRESSE_POLICE ?></p>
			<p>Si vous utilisez un smartphone, profitez de son GPS pour vous y rendre :</p>
			<p>- activez la localisation</p>
			<p>- cliquez sur le plan ci-dessous</p>
			<p>- puis sur l'icône &nbsp;<img src='img/icones/itineraire.png' alt='itinéraire'></p>
			<p>... et laissez-vous guider.</p>
			<iframe src=<?= IFRAME_MAPS_POLICE ?> width='600' height='450' title='nouvelle page google map' allowfullscreen></iframe>
		<?php endif ?>

		</section>
	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>