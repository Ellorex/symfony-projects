<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;

/**
*@Route("/category")
*/
class CategoryController extends Controller
{
    /**
     * @Route("/index", name="index_category")
     */
    public function indexAction()
    {
      $category = $this->getDoctrine()
        ->getRepository(Category::class)->findAll();
      return $this->render('AppBundle:Category:index.html.twig', array(
        'category' => $category
      ));
    }

    /**
     * @Route("/add", name="add_category")
     */
    public function addAction(Request $request)
    {
      $category = new Category();
    // la méthode createFormBuilder permet de créer un formulaire en pur PHP OO (pas de balise HTML)
      $form = $this->createFormBuilder($category)
        ->add('name', TextType::class)
        ->add('submit', SubmitType::class, array(
          'label' => 'Enregistrer'
        ))
        ->getForm();
        $form->handleRequest($request);
        // méthode permettant de savoir si le formulaire a été soumis, équivalent $request->getMethod() == 'POST' lorsqu'on utilise l'objet $request de la classe Request.
      if($form->isSubmitted()) {

        $category = $form->getData(); //permet l'hydratation automatique
        //enregistrement en db
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        return $this->redirectToRoute('index_category');
      }

      return $this->render('AppBundle:Category:add.html.twig', array(
          'addform' => $form->createView()
      ));
    }

}
