<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AjoutUtilisateurType;
use App\Form\ModifUtilisateurType;
use App\Form\ImageProfilType;

use App\Entity\Utilisateur;

class UtilisateurController extends AbstractController
{

    /**
     * @Route("/user-profil/{id}", name="user-profil" , requirements={"id"="\d+"})
     */
    public function userProfil(int $id,Request $request)
    {
        $em = $this->getDoctrine();
        $repoUtilisateur = $em->getRepository(Utilisateur::class);
        $utilisateur = $repoUtilisateur->find($id);
        if ($utilisateur==null){
            $this->addFlash('notice','Utilisateur introuvable');
            return $this->redirectToRoute('accueil');
        }
        $form = $this->createForm(ImageProfilType::class);
        if ($request->isMethod('POST')) {            
            $form->handleRequest($request);            
            if ($form->isSubmitted() && $form->isValid()) {
                $file = $form->get('photo')->getData();
                try{    
                    $fileName = $utilisateur->getId().'.'.$file->guessExtension();
                    $file->move($this->getParameter('profile_directory'),$fileName); // Nous déplaçons lefichier dans le répertoire configuré dans services.yaml
                    $em = $em->getManager();
                    $utilisateur->setPhoto($fileName);
                    $em->persist($utilisateur);
                    $em->flush();
                    $this->addFlash('notice', 'Fichier inséré');

                } catch (FileException $e) {                // erreur durant l’upload            }
                    $this->addFlash('notice', 'Problème fichier inséré');
                }
            }
        }    

        if($utilisateur->getPhoto()==null){
          $path = $this->getParameter('profile_directory').'/defaut.png';
        }
        else{
            $path = $this->getParameter('profile_directory').'/'.$utilisateur->getPhoto();
        }    
        $data = file_get_contents($path);
        $base64 = 'data:image/png;base64,' . base64_encode($data);

        return $this->render('utilisateur/user-profil.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
            'base64' => $base64
        ]);
    }    


    


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

                if ($request->get('supp')!=null){
                    $utilisateur = $repoUtilisateur->find($request->get('supp'));
                    if($utilisateur!=null){
                        $em->getManager()->remove($utilisateur);
                        $em->getManager()->flush();
                        $this->addFlash('notice','Utilisateur supprimé');
                    }
                    return $this->redirectToRoute('liste_utilisateur');
                }
        
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

