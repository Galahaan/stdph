<?php

session_start(); // en début de chaque fichier utilisant $_SESSION
require_once("include/constantes.php");

?>
<!DOCTYPE html>
<html lang='fr'>
<head>
	<title><?= NOM_PHARMA ?></title>
	<meta charset='utf-8'>
	<meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= MC_CP ?>, <?= MC_1 ?>, <?= MC_2 ?>'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' rel='stylesheet' integrity='sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1' crossorigin='anonymous'>
	<link rel='stylesheet' type='text/css' href='css/style.css'>
	<link rel='shortcut icon' href='img/favicon.ico'>
</head>

<body>
	<header>
		<section>
			<a href='index.php'>
				<img src='img/croix_mauve.png' alt=''>
				<h1><?= NOM_PHARMA ?></h1>
				<h2><?= STI_PHARMA ?></h2>
			</a>
			<p id='iTelIndex'><i class='fa fa-volume-control-phone' aria-hidden='true'></i>&nbsp;&nbsp;<a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a></p>
		</section>
		<nav class='cNavigation'>
			<ul>
				<li><a href='index.php'   >Accueil </a></li>
				<li><a href='horaires.php'>Horaires</a></li>
				<li><a href='equipe.php'  >Équipe  </a></li>
				<li><a href='contact.php' >Contact </a></li>
			</ul>
		</nav>
		<div class='cBandeauConnex'>
			<?php
				if( isset($_SESSION['client']) ){

					// si le client est connecté, on affiche son nom et le lien pour se déconnecter :
					echo "<div class='cClientConnecte'>";
						echo $_SESSION['client']['prenom'] . " " . $_SESSION['client']['nom'];
					echo "</div>";

					echo "<div class='cLienConnex'>";
						echo "<a href='deconnexion.php'>déconnexion</a>";
					echo "</div>";
				}
				else{

					// si le client n'est pas connecté, on affiche le lien pour se connecter :
					echo "<div class='cClientConnecte'>";
						echo " ";
					echo "</div>";

					echo "<div class='cLienConnex'>";
						echo "<a href='connexion.php'>connexion</a>";
					echo "</div>";
				}
			?>
		</div>
	</header>

	<main>
		<section class='cGammes'><h3>Les Marques que vous trouverez à la pharmacie</h3>

			<article>
				<div class='cImageDeco'><img src='img/bebe.jpg' alt=''><h4>Univers de bébé</h4></div>
				<div>
					<img src='img/bb_picot.png' alt=''><p>
					Nous avons choisi les laits de la gamme PICOT, ils sont bien.</p>
				</div>
				<div>
					<p>Pour le soin du corps, nous vous proposons la gamme URIAGE pour la qualité de ses produits dont aucun ne contient de parabens.
					</p><img src='img/bb_uriage.png' alt=''>
				</div>
				<div>
					<img src='img/bb_bib.png' alt=''><p>
					Pour les biberons et tétines, nous conseillons les marques AVENT et MAM dont les produits ne contiennent pas de bisphénol A.</p>
				</div>
			</article>

			<article>
				<div class='cImageDeco'><img src='img/soinDuCorps.jpg' alt=''><h4>Soins du corps</h4></div>
				<div>
					<img src='img/corps_uriage_LD.png' alt=''><p>
					Nous avons sélectionné la gamme URIAGE dont aucun produit ne contient de parabens.</p>
				</div>
				<div>
					<p>Bien qu'ils ne soient pas encore très répandus, nous vous proposons des déodorants sans parabens et sans sels d'aluminium.
					</p><img src='img/corps_uriage_deo.png' alt=''>
				</div>
			</article>

		</section>
	</main>

	<footer>
		<section><h3>Coordonnées de la <?= NOM_PHARMA ?></h3>
			<p><?= NOM_PHARMA ?></p>
			<p><?= ADR_PHARMA_L1 ?></p>
			<p><?= CP_PHARMA ?> <?= VIL_PHARMA ?></p>
			<p>tel - <?= TEL_PHARMA_DECO ?></p>
			<p>fax - <?= FAX_PHARMA_DECO ?></p>
		</section>
		<section><h3>Informations sur l'editeur du site</h3>
			<p>Édition CLR - 2018</p>
		</section>
	</footer>
</body>
</html>