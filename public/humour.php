<?php

session_start(); // en début de chaque fichier utilisant $_SESSION
ini_set("display_errors", 1);  // affichage des erreurs - à virer à la mise en prod !
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
				<img id='iLogoCroix' src='img/croix_caducee.png' alt=''>
				<h1><?= NOM_PHARMA ?></h1>
				<h2><?= STI_PHARMA ?></h2>
			</a>
			<p id='iTelBandeau'><a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a><img class='cClicIndexTaille' src='img/clicIndex.png' alt=''></p>
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
		<section class='cHumour'><h3>La thérapie par l'humour ! (*)</h3>
		<?php
			// https://www.chucknorrisfacts.fr/api/api
			do{
				$url = "https://www.chucknorrisfacts.fr/api/get?data=tri:alea;type:txt;nb:1";
				$resultat = file_get_contents($url);

				if( $resultat !== false ){
					$resultat = json_decode($resultat, true);
				}
				else{
					// file_get_contents a rencontré une erreur et a retourné "false"
					$resultat = [['points' => "" , 'fact' => "Aïe, désolé, problème de serveur ..."], [0]];
				}

				// si on veut plusieurs blagues par jour :
				// foreach($resultat as $blague){
				// 	if( $blague['points'] >= 5000 ){
				// 		echo "<p>" . $blague['fact'] . " - " . $blague['points'] . "</p>";
				// 	}
				// }
				// echo "<pre>";
				// print_r($resultat);
				// echo "</pre>";
			}
			while( $resultat[0]['points'] <= 7000 );
			// echo "<p>" . $resultat[0]['points'] . " " . $resultat[0]['fact'] . "</p>";
			echo "<p>" . $resultat[0]['fact'] . "</p>";
		?>
			<p>(*) Merci à <a href='https://www.chucknorrisfacts.fr'>chucknorrisfacts.fr</a> !<br>
			Pardonnez-nous si la blague n'est pas toujours de très bon goût !..</p>
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