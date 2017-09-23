<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Pharmacie Le Reste</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
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
		<section class="pharGarde">

			<?php

			// 'http://www.3237.fr/public/p2_commune.php?cp=44300&vil=&tri=nom_comm%20ASC,%20code_postal_comm'

			// Initialisation de la ressource curl
			$c = curl_init();

			// On indique à curl quelle url on souhaite télécharger
			curl_setopt($c, CURLOPT_URL, "http://www.3237.fr");
			// curl_setopt($c, CURLOPT_URL, "https://www.bigouig.fr");

			// CURLOPT_COOKIESESSION ?

			// On indique à curl de nous retourner le contenu de la requête
			// (sinon, curl_exec retourne seulement true ou false)
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

			// On indique à curl de retourner les headers http de la réponse dans la chaine de retour
			curl_setopt($c, CURLOPT_HEADER, true);

			// On execute la requete
			$output = curl_exec($c);


			// // Si on a une erreur, alors on la lève
			// if( $output === false ){

			// 	trigger_error('Erreur curl : '.curl_error($c),E_USER_WARNING);
			// }
			// else{
			// 	$cookie3237 = substr($output, strpos($output, 'PHPSESSID'), 30);
			// 	echo "<br>" . $cookie3237 . "<br>";
			// }

			// On ferme la ressource
			curl_close($c);



			// /*Initialisation de la ressource curl*/
			// $c = curl_init();
			// /*On indique à curl quelle url on souhaite télécharger*/
			// curl_setopt($c, CURLOPT_URL, "http://www.3237.fr");
			// /*On indique à curl de nous retourner le contenu de la requête plutôt que de l'afficher*/
			// curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			// /*On indique à curl de retourner les headers http de la réponse dans la chaine de retour*/
			// curl_setopt($c, CURLOPT_HEADER, true);
			// /*On execute la requete*/
			// $output = curl_exec($c);
			// /*On a une erreur alors on la lève*/
			// if($output === false)
			// {
			// 	trigger_error('Erreur curl : '.curl_error($c),E_USER_WARNING);
			// }
			// /*Si tout s'est bien passé on affiche le contenu de la requête*/
			// else
			// {
			// 	var_dump($output);
			// }
			// /*On ferme la ressource*/ 
			// curl_close($c);
			?>

		</section>

		<section>
			<pre>
			<?php
				//var_dump($output);
				$cookie3237 = substr($output, strpos($output, 'PHPSESSID='), 50);  // PHPSESSID=bla...blabla ; bliblibli...
				echo "<br>COOKIE 3237 = " . $cookie3237 . "<br>";

				// strstr() pourrait servir à ne garder que ce qui précède le ';' mais on va faire autrement :
				$position1 = 1 + strpos( $cookie3237, '=');
				$longueur = strpos( $cookie3237, ';') - $position1;
				$cookie3237 = substr($cookie3237, $position1, $longueur);
				echo "<br>PHPSESSID = " . $cookie3237 . "<br>";
			?>
			</pre>
		</section>

	</main>

	<footer>
		<section>
			<p>Pharmacie Le Reste</p>
			<p>21 rue du Bêle</p>
			<p>44300 Nantes</p>
			<p>tel - 02 40 25 15 80</p>
			<p>fax - 02 40 30 06 56</p>
		</section>
		<section>
			<p>Édition CLR - 2017</p>
		</section>
	</footer>
</body>
</html>