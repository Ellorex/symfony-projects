<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Retailor;
use Symfony\Component\HttpFoundation\Request;

/**
*@Route("/retailor")
*/
class RetailorController extends Controller
{
    /**
     * @Route("/index", name="retailor_index")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Retailor:index.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/add", name="add_retailor")
     */
    public function addAction(Request $request)
    {
      $retailor = new Retailor();
    // la méthode createFormBuilder permet de créer un formulaire en pur PHP OO (pas de balise HTML)
      $form = $this->createFormBuilder($retailor)
        ->add('name', TextType::class)
        ->add('fruit', EntityType::class, array(
          'class' => 'AppBundle:Fruit',
          'choice_label' => 'name'
        ))
        ->add('submit', SubmitType::class, array(
          'label' => 'Enregistrer'
        ))
        ->getForm();
        $form->handleRequest($request);
        // méthode permettant de savoir si le formulaire a été soumis, équivalent $request->getMethod() == 'POST' lorsqu'on utilise l'objet $request de la classe Request.
      if($form->isSubmitted()) {

        $retailor = $form->getData(); //permet l'hydratation automatique
        //enregistrement en db
        $em = $this->getDoctrine()->getManager();
        $em->persist($retailor);
        $em->flush();

        return $this->redirectToRoute('retailor_index');
      }

      return $this->render('AppBundle:Retailor:add.html.twig', array(
          'addform' => $form->createView()
      ));
    }

}
