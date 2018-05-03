<?php

include('inclus/entete.php');

?>
	<main id='iMain'>
		<?php $heure = heureActuelle('d'); ?>

		<section id='iPdG3237' class='cSectionContour'>

		<?php // si les gardes fonctionnent sans passer par le commissariat, ou si on est dans la journée : ?>
		<?php if( (HEURE_SOIR_POLICE_D == "X") || ((HEURE_MATIN_POLICE_D <= $heure) && ($heure < HEURE_SOIR_POLICE_D)) ) : ?>
			<p>Trouvez la&nbsp;<h3 id='iPdGh31'>pharmacie de garde</h3> la plus proche de chez vous en cliquant sur la croix ci-dessous :</p>
			<p id='iPdGcroix'>
				<a href='http://www.3237.fr/'>
					<img src='img/icones/croix_garde.png' alt=''>
					<span class='cBraille'>croix</span>
				</a>
			</p>
		<?php else : // on est en horaires de garde -> on affiche juste le titre ?>
			<h3 id='iPdGh32'>Pharmacie de garde</h3>
		<?php endif ?>

		</section>

		<section id='iPdGplan' class='cSectionContour'><h3>Localiser le commissariat de police</h3>

		<?php // si les gardes fonctionnent sans passer par le commissariat, il n'y a RIEN d'autre à afficher ?>
		<?php if( HEURE_SOIR_POLICE_D != "X" ) : ?>
			<?php // quelle que soit l'heure, on informe les gens du fonctionnement en horaires de garde, et on propose le plan : ?>
			<p>À partir de <span><?= HEURE_SOIR_POLICE_H ?></span>, et jusqu'à <span><?= HEURE_MATIN_POLICE_H ?></span> le lendemain matin, il faut se rendre, avec une pièce d'<span>identité</span> et une <span>ordonnance</span>, au <span>commissariat de police</span> situé :</p>
			<p><?= ADRESSE_POLICE ?></p>
			<p>Si vous utilisez un smartphone, profitez de son GPS pour vous y rendre :</p>
			<p>- cliquez sur le plan ci-dessous</p>
			<p>- puis sur l'icône &nbsp;<img src='img/icones/itineraire.png' alt='itinéraire'></p>
			<p>... et laissez-vous guider.</p>
			<iframe src='https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2709.4418958453143!2d-1.5537605841504296!3d47.227501579161355!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4805ee99ed6c5d25%3A0x18995709d53782b2!2sCommissariat+de+Police+Central+de+Nantes!5e0!3m2!1sfr!2sfr!4v1517266865893' width='600' height='450' title='nouvelle page google map' allowfullscreen></iframe>
		<?php endif ?>

		</section>
	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>