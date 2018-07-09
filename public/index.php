<?php

include('inclus/enteteP.php');

include('inclus/enteteH.php');
?>
	<main id='iMain'>
		<?php
			$aujourdhui = dateFr();				// fonction qui génère une date de la forme : vendredi 2 juillet 2017
			$auj = substr($aujourdhui, 0, 3);	// on garde les 3 1ères lettres de la chaîne (en vue de l'appel de 'ouverturePharmacie')

			$heure  = heureActuelle("");		// heure au format "décimal"
		?>
		<section id='iIndex' class='cSectionContour'>
			<p><?= ouverturePharmacie( $auj, $heure ) ?></p>

			<div>
			<p class='cInLi'>Vous trouverez ci-dessous les &nbsp;</p><h2 class='cInLi'>services proposés par l'officine</h2>
			<p class='cInLi'>, comme l'<strong>ordonnance en ligne</strong> et la <strong>commande en ligne</strong>, qui nécessitent un compte client, mais aussi les <strong>pharmacies de garde</strong>, les gammes de produits que nous suivons, des informations et conseils, les promotions en cours.</p>
			</div>
			<br><br><?php // je sais, ces <br> sont affreux, mais depuis le 'inline' des p et h2 ci-dessus, je n'ai pas mieux ! ?>

			<article>
				<a href= <?= ( isset($_SESSION['client']) ) ? "'prepaOrdonnance.php'" : "'connexion.php' rel='nofollow'" ?> ><h3>Préparation d'ordonnance</h3></a>
				<img src='img/index/prepaOrdonnance.jpg' alt=''>
			</article>

			<article>
				<a href= <?= ( isset($_SESSION['client']) ) ? "'prepaCommande.php'" : "'connexion.php' rel='nofollow'" ?> ><h3>Préparation de commande</h3></a>
				<img src='img/index/prepaCommande.jpg' alt=''>
			</article>

			<article>
				<a href='pharmaDeGarde.php'><h3>Pharmacies de garde</h3></a>
				<img src='img/index/pharmaDeGarde.jpg' alt=''>
			</article>

			<article>
				<a href='promos.php'><h3>Promos</h3></a>
				<img src='img/index/promos.jpg' alt=''>
			</article>

			<article>
				<a href='gammesProduits.php'><h3>Les gammes de produits</h3></a>
				<img src='img/index/gammesProduits.jpg' alt=''>
			</article>

			<article>
				<a href='infos.php'><h3>Informations / Conseils</h3></a>
				<img src='img/index/questions.jpg' alt=''>
			</article>

			<?php
				// <article>
				// 	<a href='materiel-medical.php'><h3>Matériel médical</h3></a>
				// 	<img src='img/index/materiel.jpg' alt=''>
				// </article>

				// <article>
				// 	<a href='contention.php'><h3>Contention</h3></a>
				// 	<img src='img/index/contention.jpg' alt=''>
				// </article>
			?>
 		</section>
	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>