<?php

define('NOM_PHARMA', "Pharmacie des Tilleuls");
define('PHIE_URL', "https://www.bigouig.fr");

?>

<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>

    <title><?= NOM_PHARMA ?> - Erreur 404</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel='stylesheet' type='text/css' href='<?= PHIE_URL ?>/css/theme.css'>
    <link rel='stylesheet' type='text/css' href='<?= PHIE_URL ?>/css/style.css'>
    <link rel='shortcut icon' href='img/icones/favicon.ico'>
</head>

<body>
    <header>
        <section class='cBraille'>Erreur 404 : 'page non trouvée'.
            <p><a href='<?= PHIE_URL ?>'>retour à l'accueil</a></p>
        </section>
        <section>
            <h1><?= NOM_PHARMA ?></h1>
        </section>
    </header>

    <main id='iMain'>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>Erreur 404 : 'page non trouvée'.</p>
        <p>&nbsp;</p>
        <p>Veuillez nous excuser pour ce désagrément.</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p><a href='<?= PHIE_URL ?>'>=> retour à l'accueil</a></p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
    </main>
</body>
</html>