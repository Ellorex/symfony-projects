<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use AppBundle\Entity\Producer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
*@Route("/producer")
*/

class ProducerController extends Controller
{
    /**
     * @Route("/index", name="producer_index")
     */
    public function indexAction()
    {
        $producers = $this->getDoctrine()
          ->getRepository(Producer::class)->findAll();
        return $this->render('AppBundle:Producer:index.html.twig', array(
          'producers' => $producers
        ));
    }

    /**
     * @Route("/add", name="add_producer")
     */
    public function addAction(Request $request) //quand une méthode doit gérer une soumission de formulaire, il faut utiliser un objet Request
    {
        $producer = new Producer();
      // la méthode createFormBuilder permet de créer un formulaire en pur PHP OO (pas de balise HTML)
        $form = $this->createFormBuilder($producer)
          ->add('name', TextType::class)
          ->add('email', EmailType::class)
          ->add('logo', FileType::class)
          ->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer'
          ))
          ->getForm();
          $form->handleRequest($request);
          // méthode permettant de savoir si le formulaire a été soumis, équivalent $request->getMethod() == 'POST' lorsqu'on utilise l'objet $request de la classe Request.
        if($form->isSubmitted()) {

          $producer = $form->getData(); //permet l'hydratation automatique
          $file = $producer->getLogo(); // récupération de l'objet UploadedFile placé dans la propriété logo du $producer.
          $filename = 'logo_' . strtolower($producer->getName()) . '.' . $file->guessExtension();
          var_dump($this->getParameter('dir_logo'));
          $file->move($this->getParameter('dir_logo'), $filename);
          // getParameter récupère la valeur d'une clef définie dans le fichier config.yml . La méthode move() prend deux arguments : destination du fichier et nom du fichier.

          $producer->setLogo($filename);

          $str_len = strlen($producer->getName());
          $cond1 = $str_len >= 3;
          $cond2 = $str_len <= 10;
          $condtotal = $cond1 && $cond2;

          if (!$condtotal) {
            return new Response("Le nom du producteur doit avoir entre 3 et 10 caractères.");
          }
          if (strlen($producer->getName()) <3) {
            return new Response("Le nom doit comporter au moins 3 caractères");
          }
          //enregistrement en db
          $em = $this->getDoctrine()->getManager();
          $em->persist($producer);
          $em->flush();

          //return $this->redirectToRoute('producer_index');
        }

        return $this->render('AppBundle:Producer:add.html.twig', array(
            'addform' => $form->createView(),
            'message' => 'Simple texte',
            'color' => '#FF0000'
        ));
    }

    /**
     * @Route("/edit")
     */
    public function editAction()
    {
        return $this->render('AppBundle:Producer:edit.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Producer:delete.html.twig', array(
            // ...
        ));
    }

    /**
    *@Route("/details/{id}", name="producer_details")
    */
    public function detailsAction($id) {
      $producer = $this->getDoctrine()
       ->getRepository(Producer::class)
       ->find($id);

      return $this->render('AppBundle:Producer:details.html.twig', array(
        'producer' => $producer
      ));
    }
}
