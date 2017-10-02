<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Pharmacie Le Reste</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="shortcut icon" href="img/favicon.ico">
</head>

<body>
	<header>
		<section>
			<a href="index.php">
				<img src="img/croix_mauve.png" alt="">
				<h1>Pharmacie Le Reste</h1>
				<h2>Nantes, quartier Saint-Joseph de Porterie</h2>
			</a>
			<p id="telIndex"><i class="fa fa-volume-control-phone" aria-hidden="true"></i>&nbsp;&nbsp;<a href="tel:+33240251580">02 40 25 15 80</a></p>
		</section>
		<nav class="navigation">
			<ul>
				<li><a href="index.php"   >Accueil </a></li>
				<li><a href="horaires.php">Horaires</a></li>
				<li><a href="equipe.html"  >Équipe  </a></li>
				<li><a href="contact.php"  >Contact </a></li>
			</ul>
		</nav>
	</header>

	<main>
		<section class="humour"><h3>La thérapie par l'humour ! (*)</h3>
		<?php
			// https://www.chucknorrisfacts.fr/api/api
			do{
				$url = "https://www.chucknorrisfacts.fr/api/get?data=tri:alea;type:txt;nb:1";
				try {
					$resultat = file_get_contents($url);
				} catch (Exception $e) {
					echo "file_get_contents : " . $e->getMessage() . "<br>";
				}

				if( $resultat !== false ){
					$resultat = json_decode($resultat, true);
				}
				else{
					// file_get_contents a rencontré une erreur et a retourné "false"
					$resultat = [["points" => "8000" , "fact" => "Aïe, désolé, problème de serveur ..."], [0]];
				}

				// foreach($resultat as $blague){
				// 	if( $blague['points'] >= 5000 ){
				// 		echo "<p>" . $blague['fact'] . " - " . $blague['points'] . "</p>";
				// 	}
				// }
				// echo "<pre>";
				// print_r($resultat);
				// echo "</pre>";
			}
			while( $resultat[0]['points'] <= 5000 );
			echo "<p>" . $resultat[0]['points'] . " - " . $resultat[0]['fact'] . "</p>";
			// echo "<p>" . $resultat[0]['fact'] . "</p>";
		?>
			<p>(*) Merci à <a href="https://www.chucknorrisfacts.fr">chucknorrisfacts.fr</a> !<br>
			Pardonnez-nous si la blague n'est pas toujours de très bon goût !..</p>
		</section>
	</main>

	<footer>
		<section><h3>Coordonnées de la pharmacie Le Reste</h3>
			<p>Pharmacie Le Reste</p>
			<p>21 rue du Bêle</p>
			<p>44300 Nantes</p>
			<p>tel - 02 40 25 15 80</p>
			<p>fax - 02 40 30 06 56</p>
		</section>
		<section><h3>Informations sur l'editeur du site</h3>
			<p>Édition CLR - 2017</p>
		</section>
	</footer>
</body>
</html>