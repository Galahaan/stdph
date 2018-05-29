<?php

session_start(); // en début de chaque fichier utilisant $_SESSION

//////////////////////////////////     § anti-aspiration du site     /////////////////////////////////

// si $_SESSION['bot'] est définie, c'est que l'on revient de tapette.php,
// ie que la tapette s'est déclenchée, on veut donc en savoir plus !
// => on s'envoie un mail avec les IP et les noms de domaine du robot

if( isset($_SESSION['bot']) ){

    // histoire de n'envoyer le mail qu'une seule fois, si jamais le robot insiste :
    if( !isset($_SESSION['bot']['mailEnvoye']) ){

        $_SESSION['bot']['mailEnvoye'] = true;

        $contenu =  "<html><head><title>" . date('D j M Y') . " - " . date('G\hi') .
                        " - Moteur de recherche ou Aspirateur ?</title></head>" .
                    "<body><br><br>" .
                        "IP 1      : " . $_SESSION['bot']['ip1'] . "<br>" .
                        "Domaine 1 : " . $_SESSION['bot']['do1'] . "<br>";

        if( $_SESSION['bot']['isAspi'] == true ){
            // si on est dans ce cas, c'est que les 2 IP sont différentes, donc on complète le mail :
            $contenu .=
                        "IP 2      : " . $_SESSION['bot']['ip2'] . "<br>" .
                        "Domaine 2 : " . $_SESSION['bot']['do2'] . "<br>" .
                        "<br>Ce robot a donc été bloqué ..." .
                    "</body></html>";
        }

        mail( $_SESSION['bot']['mailDest'],
              date('D j M Y') . " - " . date('G\hi') . " - passage d'un robot chez " . $_SESSION['bot']['url'],
              $contenu,
              "From: " . mb_encode_mimeheader($_SESSION['bot']['url'], "UTF-8", "B") .
              "<" . $_SESSION['bot']['mailExp'] . ">" .
              "\r\nReply-To: \r\nContent-Type: text/html; charset=\"UTF-8\"\r\n"
            );
    }
}

if( $_SESSION['bot']['isAspi'] == true ){

    // on bloque l'affichage après un petit message
    echo "sorry";
    exit();
}
//////////////////////////////////////////////////////////////////////////////////////////////////////





ini_set("display_errors", 1);  // affichage des erreurs - à virer à la mise en prod !

?>

<!-- Ce § a été copié sur la page des captcha de google  -->
<!-- je pense que c'est celui dont j'ai besoin           -->
<!-- il faut regarder de plus près les scripts JS ...    -->

<!-- https://developers.google.com/recaptcha/docs/invisible -->
<!-- Invoking the invisible reCAPTCHA challenge after client side validation. -->

<html>
<head>
<script>
  function onSubmit(token) {
    alert('thanks ' + document.getElementById('field').value);
  }

  function validate(event) {
    event.preventDefault();
    if (!document.getElementById('field').value) {
      alert('You must add text to the required field');
    } else {
      grecaptcha.execute();
    }
  }

  function onload() {
    var element = document.getElementById('submit');
    element.onclick = validate;
  }
</script>
<script src='https://www.google.com/recaptcha/api.js' async defer></script>
</head>
<body onload='placerFocus("iFocus")'>
   <form>
     Name: (required) <input id='field' name='field'>
     <div id='recaptcha' class='g-recaptcha'
          data-sitekey='6LcPQyUUAAAAAPTt3tR1KVuHoq9XVMs-74gHSOxY'
          data-callback='onSubmit'
          data-size='invisible'></div>
     <button id='submit'>submit</button>
   </form>
    <script>onload();</script>
</body>
</html>


<?php

///////////////////////////////////////////////////////////////////////////////////////////////
/////     INCLUDE sécurisé
///////////////////////////////////////////////////////////////////////////////////////////////

if( empty($page) ){
$page = "fonctions"; // page à inclure : fonctions.php qui lui-même inclut constantes.php

// On construit le nom de la page à inclure en prenant 2 précautions :
// - ajout dynamique de l'extension .php
// - on supprime également d'éventuels espaces en début et fin de chaîne
$page = trim($page.".php");
}

// On remplace les caractères qui permettent de naviguer dans les répertoires
$page = str_replace("../","protect",$page);
$page = str_replace(";","protect",$page);
$page = str_replace("%","protect",$page);

// On interdit l'inclusion de dossiers protégés par htaccess.
// S'il s'agit simplement de trouver la chaîne "admin" dans le nom de la page,
// strpos() peut très bien le faire, et surtout plus vite !
// if( preg_match("admin", $page) ){
if( strpos($page, "admin") ){
    echo "Vous n'avez pas accès à ce répertoire";
}
else{
    // On vérifie que la page est bien sur le serveur
    // (à propos des chemins, même si header.php est colatéral à fonctions.php,
    //  comme il est inclus à partir d'un fichier situé un cran au-dessus, c'est
    //  cette première position qui compte)
    if (file_exists("inclus/".$page) && $page != "index.php") {
        require_once("./inclus/".$page);
    }
    else{
        echo "Erreur Include : le fichier " . $page . " est introuvable.";
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////
/////     FIN INCLUDE sécurisé
///////////////////////////////////////////////////////////////////////////////////////////////

// on détermine la page courante ...
// 1° => pour souligner le mot dans le menu de nav. : $pageCourante['flag']
// 2° => pour compléter le 'title' et le menu destinés à l'accessibilité : $pageCourante['nom']
$pageCourante = pageCourante($_SERVER['REQUEST_URI']);

?>
<!DOCTYPE html>
<html lang='fr'>
<head>
    <title><?= NOM_PHARMA . LOC_PHARMA_1 . LOC_PHARMA_2 . " - " . $pageCourante['nom'] ?></title>
    <meta charset='utf-8'>

    <!-- Mots clés de la page -->
    <meta name='keywords' content='pharmacie, <?= MC_NOM_PHARMA ?>, <?= MC_QUARTIER ?>, <?= MC_1 ?>, <?= MC_2 ?>, <?= MC_3 ?>, <?= $pageCourante['nom'] ?>'>

    <!-- Prise en compte du responsive design -->
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <!-- intégrer le CDN de fontAwesome -->
    <!-- on le place AVANT l'appel à notre CSS pour se donner la possibilité -->
    <!-- de le modifier dans notre CSS puisque le fichier HTML est lu de haut en bas -->
    <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' rel='stylesheet' integrity='sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1' crossorigin='anonymous'>
    <link rel='stylesheet' type='text/css' href='css/theme.css'>
    <link rel='stylesheet' type='text/css' href='css/style.css'>
    <link rel='shortcut icon' href='img/icones/favicon.ico'>

    <!-- <script src='https://www.google.com/recaptcha/api.js' async defer></script> -->
<!--    <script>
        function onSubmit(token) {
            document.getElementById("goocapt").submit();
        }
    </script> -->
</head>

<body onload='placerFocus("iFocus")'>
    <header>
        <div id='iPiegeAA'><a href='tapette.php'><img src='img/bandeau/tapette.png'></a></div>
        <nav class='cBraille'><?= $pageCourante['nom'] ?>
            <ol>
                <li><a href='aide.php'     accesskey='h'>[h] Aide à la navigation dans le site</a></li>
                <li><a href='#iNavigation' accesskey='n'>[n] Menu de navigation</a></li>
                <li><a href='#iLienConnex' accesskey='x'>[x] Connexion/Inscription/Deconnexion</a></li>
                <li><a href='#iMain'       accesskey='c'>[c] contenu de <?= $pageCourante['nom'] ?></a></li>
            </ol>
        </nav>

        <section>
            <a href='index.php' accesskey='r'>
                <img id='iLogoCroix' src='img/bandeau/croix_caducee.png' alt=''>
                <h1><?= NOM_PHARMA ?></h1>
                <p><?= LOC_PHARMA_2 ?></p>
            </a>
            <p id='iTelBandeau'><a href='tel:<?= TEL_PHARMA_UTIL ?>'><?= TEL_PHARMA_DECO ?></a><img class='cClicIndexTaille' src='img/icones/clicIndex.png' alt=''></p>
        </section>
        <nav id='iNavigation'>
            <ul>
                <li><a <?= ($pageCourante['flag'] == "1000") ? "id = 'iPageCourante'" : "" ?> href='index.php'   >Accueil </a></li>
                <li><a <?= ($pageCourante['flag'] == "0100") ? "id = 'iPageCourante'" : "" ?> href='horaires.php'>Horaires</a></li>
                <li><a <?= ($pageCourante['flag'] == "0010") ? "id = 'iPageCourante'" : "" ?> href='equipe.php'  >Équipe  </a></li>
                <li><a <?= ($pageCourante['flag'] == "0001") ? "id = 'iPageCourante'" : "" ?> href='contact.php' >Contact </a></li>
            </ul>
        </nav>
        <div id='iBandeauConnex'>
            <?php
                if( isset($_SESSION['client']) ){

                    // si le client est connecté, on affiche son nom et le lien pour se déconnecter :
                    echo "<div id='iClientConnecte'>";
                        echo $_SESSION['client']['prenom'] . " " . $_SESSION['client']['nom'];
                    echo "</div>";

                    echo "<div id='iLienConnex'>";
                        echo "<a href='deconnexion.php'>déconnexion</a>";
                    echo "</div>";
                }
                else{

                    // si le client n'est pas connecté, on affiche le lien pour se connecter :
                    // (normalement, pour prepaOrdo et prepaCom, on ne peut arriver là sans être déjà connecté)
                    echo "<div id='iClientConnecte'>";
                        echo " ";
                    echo "</div>";

                    echo "<div id='iLienConnex'>";
                        echo "<a href='connexion.php'>connexion</a>";
                    echo "</div>";
                }
            ?>
        </div>
    </header>
