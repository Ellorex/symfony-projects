$(document).ready(function() {
  // dom chargé

  var app = {
    server: 'http://localhost:8000',
    data: {
      fruits: null
    }
  };


  // 1. ciblage et mise en cache
  var appHtml       = $('div#appHtml');
  var btnTestAjax   = appHtml.find('#btnTestAjax');
  var btnFruitList  = appHtml.find('button#btnFruitList');
  var fruitDisplay  = appHtml.find('div#fruitDisplay');
  var displaySelect = appHtml.find('select#displaySelect');


  // 2. fonctions

  function init() {
    ajaxFruitList(); //appelle la fonction de récupération des fruits
  }

  var ajaxFn = function() {

    // Une requête serveur de redirection est envoyée. Lorsque la réponse est reçue, alors la fonction du deuxieme argument est effectuée
    $.get(app.server + '/fruits/api/json', function(res) {
      console.log(res);
      console.log(typeof res);
      var fruit = JSON.parse(res);
      console.log(fruit);
      console.log(typeof fruit);
      console.log('Nom du fruit : ' + fruit.name);

      var fruitData = 'Nom : ' + fruit.name;
      fruitData += '<br/>' + 'Origine : ' + fruit.origin;
      fruitDisplay.html('<strong>' + fruitData + '</strong>');
    });
  }

  var ajaxFruitList = function() {
    // si les données ne sont pas déjà stockées, on les demande au serveur
    if (!app.data.fruits) {
      $.get(app.server + '/fruits/api/list', function(res2) {
        var fruits = JSON.parse(res2);
        app.data.fruits = fruits;
        fruitDisplay.html(transformToHtml(fruits, displaySelect.val()));
        $('span.displayFruit').click(function() {
          console.log('zozo');
        });

      });
    } else {
      fruitDisplay.html(transformToHtml(app.data.fruits, displaySelect.val()));
      $('span.displayFruit').click(function() {
        console.log('zozo');
      });

    }
  }

  var transformToHtml = function(fruits, type) {
    var output = '';
    if (type == 'list') {
      output += '<ul>';
      fruits.forEach(function(fruit) {
        output += '<li>' + fruit.name + '</li>';
      });
      output += '</ul>';
    }

    if (type == 'table') {

      output += '<table id="fruitTable" class="table table-bordered">';
      output += '<tr>';
      output += '<th>' + 'Nom' + '</th>';
      output += '<th>' + 'Origine' + '</th>';
      output += '<th>' + 'Comestible' + '</th>';
      output += '<th>' + 'Producteur' + '</th>';
      output += '</tr>';

      // itération sur fruits
      fruits.forEach(function(fruit) {
        output += '<tr>';
        output += '<td><span class="displayFruit">' + fruit.name + '</span></td>';
        output += '<td>' + fruit.origin + '</td>';
        if (fruit.eatable == true) {
          output += '<td>' + "Oui" + '</td>';
        } else {
          output += '<td>' + "Non" + '</td>';
        }
        if (fruit.producer) {
        output += '<td>' + fruit.producer + '</td>';
        } else {
          output += '<td>' + "Non renseigné" + '</td>';
        }
        });
      output += '</tr>';
      output += '</table>';
    }
    return output;
  }
  // 3. événements
  // au clic sur le bouton btnTestAjax, la fonction ajaxFn est déclenchée
  var displayFruit  = appHtml.find('span.displayFruit');

  var fruitDetails = function() {
    console.log('HJHUHU');
  }

  btnTestAjax.click(ajaxFn);
  btnFruitList.click(ajaxFruitList);
  displaySelect.change(ajaxFruitList);
  // displayFruit.click(fruitDetails);

  init();
});
