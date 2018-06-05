<?php

define('NOM_PHARMA', "Pharmacie des Tilleuls");
define('URL', "https://www.bigouig.fr");

?>

<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>

    <title><?= NOM_PHARMA ?> - Erreur index</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel='stylesheet' type='text/css' href='<?= URL ?>/css/theme.css'>
    <link rel='stylesheet' type='text/css' href='<?= URL ?>/css/style.css'>
    <link rel='shortcut icon' href='img/icones/favicon.ico'>
</head>

<body>
    <header>
        <section class='cBraille'>Erreur : le serveur n'a pas trouvé la page index.php.
            <p><a href='<?= URL ?>'>retour à l'accueil</a></p>
        </section>
        <section>
            <h1><?= NOM_PHARMA ?></h1>
        </section>
    </header>

    <main id='iMain'>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>Erreur : le serveur n'a pas trouvé la page index.php.</p>
        <p>&nbsp;</p>
        <p>Veuillez nous excuser pour ce désagrément. Nous vous invitons à consulter le site un peu plus tard.</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p><a href='<?= URL ?>'>=> retour à l'accueil</a></p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
    </main>
</body>
</html>