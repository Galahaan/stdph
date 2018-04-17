

///////////////////////////////////////////////////////////////////////////////////////////////
//
//                          placerFocus( id )
//
// Cette fonction a été créée dans un but d'accessibilité du site aux lecteurs d'écran.
// (braille ou autre ...)
// Elle est utilisée en cas d'envoi de formulaires (par exemple) pour positionner
// le focus directement sur le message de confirmation.
// Ca permet aux aveugles de lire immédiatement le message de confirmation, sans avoir à lire
// tout le début inutile de la page.
//
// (HTML5 propose bien l'attribut de balise 'autofocus', mais celui-ci ne fonctionne que
//  sur les balises de type <input> :/ )
//
// La fonction prend comme paramètre d'entrée l'id de l'élément sur lequel on veut placer
// le focus.
//
///////////////////////////////////////////////////////////////////////////////////////////////
function placerFocus( id ) {
    document.getElementById(id).focus();
}
