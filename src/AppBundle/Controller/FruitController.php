<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Fruit;
use AppBundle\Entity\Producer;
use AppBundle\Entity\Category;

/**
*@Route("/fruits")
**/
class FruitController extends Controller {

/**
*@Route("/", name="fruit_admin_page")
**/
  public function indexAction(Request $request) {
    $post = $request->request;
    if($request->getMethod() == 'POST') {
      $name = $post->get('name');
      $origin = $post->get('origin');
      $eatable = $post->get('eatable');
      $producer_id = $post->get('producer_id');

      // On récupère un tableau d'identifiants de catégories, correspondant au-x case-s cochée-s
      $categories_posted = $post->get('categories');

      //récupérer l'objet producer complet à partir d'un id
      $producer = $this->getDoctrine()
      ->getRepository(Producer::class)->find($producer_id);



      if ($eatable) {
        $eatable = 1;
      } else {
        $eatable = 0;
      }

      $fruit = new Fruit();
      // ça hydrate
      $fruit->setName($name);
      $fruit->setOrigin($origin);
      $fruit->setEatable($eatable);
      $fruit->setProducer($producer);

      if ($categories_posted) {
        foreach ($categories_posted as $c) {
          $category = $this->getDoctrine()->getRepository(Category::class)->find($c);
          $fruit->addCategory($category);
        }
      }


      // petite utilisation du EntityManager oklm
      $em = $this->getDoctrine()->getManager();

      $em->persist($fruit); //prépare la requête d'insertion mais n'exécute aucune requête SQL
     $em->flush(); //exécute les requêtes SQL en attente
    }

    // accès en lecture des fruits
    //Fruit::class retourne le chemin complet + le nom de la classe.
    $fruits = $this
      ->getDoctrine()
      ->getRepository(Fruit::class)
      ->findAll();
    $producers = $this
      ->getDoctrine()
      ->getRepository(Producer::class)
      ->findAllNotAssigned();
      $category = $this
        ->getDoctrine()
        ->getRepository(Category::class)
        ->findAll();
  return $this->render('fruit/index.html.twig', array(
    'fruits' => $fruits,
    'producer_id' => $producers,
    'category' => $category
    ));
  }

/**
*@Route("/delete/{id}", name="fruit_delete")
*/
  public function deleteAction($id) { //l'argument doit correspondre au paramètre d'url
    $fruit = $this->getDoctrine()->getRepository(Fruit::class)->find($id);
    $em = $this->getDoctrine()->getManager();
    $em->remove($fruit); //requête de suppression en attente. Pas d'intéraction avec le serveur
    $em->flush(); //exécute les requêtes en attente

    return $this->redirectToRoute('fruit_admin_page');
  }

/**
*@Route("/update/{id}", name="fruit_update")
*/
 public function updateAction($id, Request $request) {
   $em = $this->getDoctrine()->getManager();
   $fruit = $em->getRepository(Fruit::class)->find($id);
   //appeler getRepository depuis getManager établit une connexion, une visibilité entre le repo et le manager. Ici, le manager est au courant, est notifié de l'existence de l'objet fruit, si cet objet change (cad reçoit de nouvelles valeurs), le manager le sait. Le manager surveille cet objet.
   if($request->getMethod() == 'POST') {
     $fruit->setName($request->request->get('name'));
     $fruit->setOrigin($request->request->get('origin'));

     $eatable = ($request->request->get('eatable')) ? 1 : 0;
      $fruit->setEatable($eatable);

      $em->flush();
      return $this->redirectToRoute('fruit_admin_page');
     } // le manager exécutera la reqûete sql appropriée si l'objet $fruit a été modifié.
   return $this->render('fruit/update.html.twig', array(
     'fruit' => $fruit
   ));
 }

 /**
 *@Route("/details/{id}", name="fruit_details")
 */
 public function detailsAction($id) {
   $fruit = $this->getDoctrine()
    ->getRepository(Fruit::class)
    ->find($id);

   return $this->render('fruit/details.html.twig', array(
     'fruit' => $fruit
   ));
 }

 /**
 *@Route("/category/{name}", name="frategory")
 */
 public function displayCatAction($name) {
   $fruits = $this->getDoctrine()
    ->getRepository(Fruit::class)
    ->findByCategoryName($name);

   return $this->render('fruit/category.html.twig', array(
     'name' => $name,
     'fruits' => $fruits
   ));
 }

 /**
 *@Route("/api/json")
 */
 public function jsonAction() {
   $fruits = ['pomme', 'poire'];
   $fruit = [
     'name' => 'Cerise',
     'origin' => 'Normandie',
     'comestible' => 'true',
     'category' => [
       array('name' => 'Cuisine'),
       array('name' => 'Voyage')
     ]
   ];
   //conversion du tableau PHP en chaîne de caractères JSON
   $fruits_json = json_encode($fruits);
   $fruit_json = json_encode($fruit);
   return new Response($fruit_json);
 }

 /**
 *@Route("/api/client")
 */
 public function clientAction() {

   return $this->render('client.html.twig');
 }

 /**
 *@Route("/api/list")
 */
 public function ajaxFruitListAction() {
   $fruits = $this->getDoctrine()
    ->getRepository(Fruit::class)
    ->findAll();

    // tentative d'encodage en JSON mais impossible d'encoder des objets PHP. Json fonctionne avec des tableaux associatifs
    // $fruits_json = json_encode($fruits);

    $fruits_array = [];
    foreach ($fruits as $f) {
      $fruit_array = [
        'name' => $f->getName(),
        'origin' => $f->getOrigin(),
        'eatable' => $f->getEatable(),
        ];
        if ($f->getProducer()) {
          $fruit_array['producer'] = $f->getProducer()->getName();
        }

      //équivalent d'un array_push
      $fruits_array[] = $fruit_array;
    }

    //encodage en json du tableau associatif
    $fruits_json = json_encode($fruits_array);
    return new Response($fruits_json);
 }
}
