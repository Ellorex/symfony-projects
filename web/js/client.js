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
  var fruitDetails  = appHtml.find('div#fruitDetails');
  var cbEatable = appHtml.find('input#cbEatable');
  var cbNonEatable = appHtml.find('input#cbNonEatable');

  var elemActive = null;


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
    fruitDetails .html('');

    // si les données ne sont pas déjà stockées, on les demande au serveur
    if (!app.data.fruits) {
      $.get(app.server + '/fruits/api/list', function(res2) {
        var fruits = JSON.parse(res2);
        app.data.fruits = fruits;
        fruitDisplay.html(transformToHtml(fruits, displaySelect.val()));
      });
    } else {
      fruitDisplay.html(transformToHtml(app.data.fruits, displaySelect.val()));
      }

    }


  var transformToHtml = function(fruits, type) {
    var output = '';
    if (type == 'list') {
      output += '<ul>';
      fruits.forEach(function(fruit) {
        output += '<span><li class="fruitName" id="'+fruit.id+'">' + fruit.name + '</li></span>';
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
        output += '<td class="fruitName" id="'+fruit.id+'">' + fruit.name + '</td>';
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
  var displayDetailFruit = function (fruit) {
    var output = '';
    output += '<p>' + fruit.name + '</p>';
    output += '<p>Origine : ' + fruit.origin + '</p>';

    if (fruit.producer) {
      output += '<p>Produit par : ' + fruit.producer.name + '</p>';
      output += '<p>Email du producteur : ' + fruit.producer.email + '</p>';
      if (fruit.producer.logo) {
        var url = app.server + '/img/logo/' + fruit.producer.logo;
        output += '<img src=" '+url+' ">';
      } else {
        output += '';
      }
    } else {
      output += '<p>Pas de producteur renseigné</p>';
    }

    fruitDetails.html(output);
  }

  var detailFruit = function() {

      var id = $(this).attr('id');
      var url = app.server + '/fruits/api/detail/' + id;

      $.get(url, function(res3) {
        var fruit = JSON.parse(res3);
        displayDetailFruit(fruit);
      });
      $(this).parent().parent().children('tr').removeClass('custom-active');
      $(this).parent().addClass("custom-active");
  }

  var filterByEatable = function() {
    var cb1 = cbEatable.prop('checked');
    var cb2 = cbNonEatable.prop('checked');

    var fruitsFiltered = app.data.fruits.filter(function(fruit) {
      return (fruit.eatable === cb1 || fruit.eatable === cb2);
    });
    fruitDisplay.html(transformToHtml(fruitsFiltered, 'table'));
  }
  btnTestAjax.click(ajaxFn);
  btnFruitList.click(ajaxFruitList);
  displaySelect.change(ajaxFruitList);

  fruitDisplay.on('click', '.fruitName', detailFruit);

  cbEatable.on('click', filterByEatable);
  cbNonEatable.on('click', filterByEatable);

  init();
});
