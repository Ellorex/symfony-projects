<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Classes\Fruit;

// prefixe de route. S'applique à toutes les routes de la classe.
/**
*@Route("/test")
**/
class TestController extends Controller {
  private $message = "Petit message";
  private $fruits = ['pêche', 'banane', 'abricot']; //tableau indexé numériquement
  private $fruits2 = array(
    array('name' => 'Mangue', 'origin' => 'Madagascar', 'eatable' => true),
    array('name' => 'Poire', 'origin' => 'Espagne', 'eatable' => true),
    array('name' => 'Baies mixtes', 'origin' => 'Papouasie', 'eatable' => false)
  );
  // private $fruits3 = array( //ERREUR. Impossible d'instancier la classe ici, il faut la faire dans le constructeur de TestController.
  //   new Fruit('Orange', 'Sicile', true),
  //   new Fruit('Tromate', 'Suceava', false),
  //   new Fruit('Citron', 'Bari', true)
  // );
private $fruits3;
  public function __construct() {
    $this->fruits3 = array(
    new Fruit('Orange', 'Sicile', true, 'https://it.wikipedia.org/wiki/Citrus_sinensis'),
    new Fruit('Tromate', 'Suceava', false, NULL),
    new Fruit('Citron', 'Bari', true, 'https://fr.wikipedia.org/wiki/Citron'),
    new Fruit('Baies Mixtes', 'Madagascar', false, NULL),
    new Fruit('Pomme', 'Normandie', true, 'https://fr.wikipedia.org/wiki/Pomme')
  );
  }

  public function getMessage() {
    return $this->message;
  }

  private function getFruitsLists() {
    $output = "<ul>";
      foreach ($this->fruits as $fruit) {
        $output .= "<li>" . $fruit . "</li>";
      }
    $output .= "</ul>";
    return $output;
  }
    // alias facultatif, permet de simplifier les routes
  /**
    *@Route("/example", name="example_page")
  **/
  public function exampleAction() {
    //return 'tatata' // erreur : retour non valide car chaîne de caractères retournée. On doit retourner un objet de type réponse
    $res = new Response("tatata"); //on transite par un objet pour pouvoir retourner une châine de caractères.
    $res2 = new Response("<h1>tadaaam</h1>");
    $res3 = new Response($this->getMessage());
    //$res4 = new Response($this->fruits); erreur : on ne peut pas renvoyer au client une structure de données PHP non convertible en string. Si un boléen est facilement convertible, ce n'est pas le cas des tableaux.

    return $this->render('test/example.html.twig', array(
      'fruits' => $this->fruits3
    ));
  }

  /**
    *@Route("/fruits/list")
  **/

  public function fruitsListAction() {
    return new Response($this->getFruitsLists());

  }

  /**
    *@Route("/fruits/static")
  **/
  public function fruitsStaticAction() {
    return $this->render('test/fruits.html'); //rennder cherche par défaut dans app/ressources/views, s'il a un 2e argument, c'est forcément un tableau associatif qui permet de donner à la vue des données  aussi bien simples (string, int etc) que complexes (tableaux, objets)

  }

  /**
    *@Route("/fruits", name ="fruits_page")
  **/
  public function fruitsAction() { //renvoie un fichier dynamique / template twig
    return $this->render('test/fruits.html.twig', array(
      'title' => 'Liste de fruits',
      'message' => $this->getMessage(),
      'fruits' => $this->fruits,
      'tata' => NULL,
      'fruits2' => $this->fruits2,
      'fruits3' => $this->fruits3
    ));

  }
  /**
    *@Route("/eatable", name ="eatable_page")
  **/
  public function eatableAction() {
    return $this->render('test/eatable.html.twig', array(
      'fruits3' => $this->fruits3
    ));
  }
}

?>
