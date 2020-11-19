<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contact;


class StaticController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(): Response
    {
        return $this->render('static/accueil.html.twig', [
            
        ]);
    }
     /**
     * @Route("/apropos", name="apropos")
     */
    public function apropos(): Response
    {
        return $this->render('static/apropos.html.twig',[

        ]);
    }
      /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, \Swift_Mailer $mailer)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('notice','Bouton appuyé');

            $message = (new \Swift_Message($form->get('subject')->getData()))
             ->setFrom($form->get('email')->getData())
            ->setTo('fabienbayon311001@gmail.com')
            ->setBody($this->renderView('contact-email.html.twig',
            array('name'=>$form->get('name')->getData(),'subject'=>$form->get('subject')
            ->getData(),'message'=>$form->get('message')->getData())), 'text/html');
            $mailer->send($message);

            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $this->redirectToRoute('contact');
            }
            } 

        return $this->render('static/contact.html.twig', [
        'form'=>$form->createView()

        ]);
    }
       /**
     * @Route("/mention", name="mention")
     */
    public function mention(): Response
    {
        return $this->render('static/mention.html.twig',[

        ]);
    }
}


