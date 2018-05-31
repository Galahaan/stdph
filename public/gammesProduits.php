<?php

include('inclus/entete.php');

?>
	<main id='iMain'>
		<section id='iGammesProd' class='cSectionContour'><h2>Les Marques que vous trouverez à la pharmacie</h2>

			<nav>
				<ul>
					<li><a href="#iGPubb">Univers de bébé</a></li>
					<li><a href="#iGPsdc">Soins du corps</a></li>
				</ul>
			</nav>

			<article id='iGPubb'>
				<div class='cImageDeco'><img src='img/gammesProduits/bebe.jpg' alt=''><h4>Univers de bébé</h4></div>
				<div>
					<img src='img/gammesProduits/bb_picot.png' alt=''><p>
					Nous avons choisi les laits de la gamme PICOT, ils sont bien.</p>
				</div>
				<div>
					<p>Pour le soin du corps, nous vous proposons la gamme URIAGE pour la qualité de ses produits dont aucun ne contient de parabens.
					</p><img src='img/gammesProduits/bb_uriage.png' alt=''>
				</div>
				<div>
					<img src='img/gammesProduits/bb_bib.png' alt=''><p>
					Pour les biberons et tétines, nous conseillons les marques AVENT et MAM dont les produits ne contiennent pas de bisphénol A.</p>
				</div>
			</article>

			<article id='iGPsdc'>
				<div class='cImageDeco'><img src='img/gammesProduits/soinDuCorps.jpg' alt=''><h4>Soins du corps</h4></div>
				<div>
					<img src='img/gammesProduits/corps_uriage_LD.png' alt=''><p>
					Nous avons sélectionné la gamme URIAGE dont aucun produit ne contient de parabens.</p>
				</div>
				<div>
					<p>Bien qu'ils ne soient pas encore très répandus, nous vous proposons des déodorants sans parabens et sans sels d'aluminium.
					</p><img src='img/gammesProduits/corps_uriage_deo.png' alt=''>
				</div>
			</article>

		</section>
	</main>

	<?php include('inclus/pdp.php'); ?>

</body>
</html>