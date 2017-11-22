if ($) console.log("J'suis al");

$(document).ready(function() {
  // dom chargé

  //stockage dans une variable de retour (=mettre en mémoire cache). Evite à JQuery de parcourir le même doc plusieurs fois
  var btnHideForm = $('#btnHideForm');
  var isFormVisible = true;

  // next permet de cibler l'élément sibling suivant. $(this) fait référence à l'élément englobant. Une alternative est de mettre un id sur la balise form
  btnHideForm.click(function() {
  $(this).next().toggle();
  isFormVisible = !isFormVisible;
  (isFormVisible)
    ? btnHideForm.html('Masquer le formulaire')
    : btnHideForm.html('Afficher le formulaire')
  });




});
