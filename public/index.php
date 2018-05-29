<?php

include('inclus/entete.php');

?>
	<main id='iMain'>
		<?php
			$aujourdhui = dateFr();				// fonction qui génère une date de la forme : vendredi 2 juillet 2017
			$auj = substr($aujourdhui, 0, 3);	// on garde les 3 1ères lettres de la chaîne (en vue de l'appel de 'ouverturePharmacie')

			$heure  = heureActuelle("");		// heure au format "décimal"
		?>
		<section id='iIndex' class='cSectionContour'>
			<p><?= ouverturePharmacie( $auj, $heure ) ?></p>

			<p  class='cIB'>Vous trouverez ci-dessous l'ensemble des &nbsp;</p>
			<h3 class='cIB'>services proposés par l'officine.</h3>

			<article>
				<a href= <?= ( isset($_SESSION['client']) ) ? "'prepaOrdonnance.php'" : "'connexion.php'" ?> ><h4>Préparation d'ordonnance</h4></a>
				<img src='img/index/prepaOrdonnance.jpg' alt=''>
			</article>

			<article>
				<a href= <?= ( isset($_SESSION['client']) ) ? "'prepaCommande.php'" : "'connexion.php'" ?> ><h4>Préparation de commande</h4></a>
				<img src='img/index/prepaCommande.jpg' alt=''>
			</article>

			<article>
				<a href='pharmaDeGarde.php'><h4>Pharmacies de garde</h4></a>
				<img src='img/index/pharmaDeGarde.jpg' alt=''>
			</article>

			<article>
				<a href='promos.php'><h4>Promos</h4></a>
				<img src='img/index/promos.jpg' alt=''>
			</article>

			<article>
				<a href='gammesProduits.php'><h4>Les gammes de produits</h4></a>
				<img src='img/index/gammesProduits.jpg' alt=''>
			</article>

			<article>
				<a href='infos.php'><h4>Informations / Conseils</h4></a>
				<img src='img/index/questions.jpg' alt=''>
			</article>

			<?php
				// matériel médical / contention ?

				// <article>
				// 	<a href='humour.php'><h4>La blague de Chuck Norris !..</h4></a>
				// 	<img src='img/index/humour.jpg' alt=''>
				// </article>
			?>
 		</section>
	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>