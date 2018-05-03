<?php

include('inclus/entete.php');

?>
	<main id='iMain'>
		<section id='iHumour' class='cSectionContour'><h3>La thérapie par l'humour ! (*)</h3>
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

	<?php include('inclus/pdp.php'); ?>

</body>
</html>