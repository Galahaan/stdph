<?php

include('inclus/headerR.php');

?>
	<main id='iMain'>
		<?php
			$aujourdhui = dateFr();				// fonction qui génère une date de la forme : vendredi 2 juillet 2017
			$auj = substr($aujourdhui, 0, 3);	// on garde les 3 1ères lettres de la chaîne (en vue de l'appel de 'ouverturePharmacie')

			$heure  = heureActuelle("");		// heure au format "décimal"
		?>
		<section id='iIndexIntro'><h3><?= ouverturePharmacie( $auj, $heure ) ?></h3></section>

		<section id='iIndexVignettes'><h3>Services proposés par la <?= NOM_PHARMA ?></h3>

			<article>
				<a href= <?= ( !empty($_SESSION) ) ? "'prepaOrdonnance.php'" : "'connexion.php'" ?> ><h4>Préparation d'ordonnance</h4></a>
				<img src='img/index/prepaOrdonnance.jpg' alt=''>
			</article>

			<article>
				<a href= <?= ( !empty($_SESSION) ) ? "'prepaCommande.php'" : "'connexion.php'" ?> ><h4>Préparation de commande</h4></a>
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

	<?php include('footer.php'); ?>

</body>
</html>