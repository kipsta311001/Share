<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AjoutUtilisateurType;
use App\Form\ModifUtilisateurType;
use App\Entity\Utilisateur;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/ajout_utilisateur", name="ajout_utilisateur")
     */
    public function ajoutUtilisateur(Request $request)
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(AjoutUtilisateurType::class,$utilisateur);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request); 
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager(); 
                $utilisateur->setDateInscription(new \DateTime());
                $em->persist($utilisateur); // Nous enregistrons notre nouveau thème
                $em->flush(); // Nous validons notre ajout 

                $this->addFlash('notice','Ajout éffectué');

            }

        return $this->redirectToRoute('ajout_utilisateur'); 
        }
        return $this->render('utilisateur/ajout_utilisateur.html.twig', [
            'form' => $form->createView()
        ]);
    }

     /**
     * @Route("/liste_utilisateur", name="liste_utilisateur")
     */
    public function listeUtilisateur(Request $request)
    {
     
                $em = $this->getDoctrine(); 
                $repoUtilisateur = $em->getRepository(utilisateur::class);
                $utilisateurs = $repoUtilisateur->findBy(array(),array('nom'=>'ASC', 'prenom'=>'ASC'));

        
        return $this->render('utilisateur/liste_utilisateur.html.twig', [
            'utilisateurs' => $utilisateurs
        ]);
    }

     /**
     * @Route("/modif_utilisateur/{id}", name="modif_utilisateur", requirements={"id"="\d+"})
     */
    public function modifUtilisateur(int $id,Request $request)
    {

            $em = $this->getDoctrine();
            $repoUtilisateur = $em->getRepository(Utilisateur::class);
            $utilisateur = $repoUtilisateur->find($id);
            if($utilisateur==null){
                $this->addFlash('notice', "Cet Utilisateur n'existe pas");
                return $this->redirectToRoute('liste_utilisateur');
            }
           
      
        $form = $this->createForm(ModifUtilisateurType::class,$utilisateur);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request); 
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($utilisateur); // Nous enregistrons notre nouveau thème
                $em->flush(); // Nous validons notre ajout 
                $this->addFlash('notice','Modification éffectué');

            }

        return $this->redirectToRoute('liste_utilisateur'); 
        }
        return $this->render('utilisateur/modif_utilisateur.html.twig', [
            'form' => $form->createView()
        ]);
    }

}

