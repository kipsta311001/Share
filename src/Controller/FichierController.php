<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AjoutFichierType;
use App\Entity\Fichier;
use Symfony\Component\HttpFoundation\Request;

class FichierController extends AbstractController
{



       /**
     * @Route("/supp-fichier/{id}", name="supp-fichier" ,requirements={"id"="\d+"})
     */
  
    public function suppFichier(int $id, Request $request): Response
    {

       
        $em = $this->getDoctrine();
        $repoFichier = $em->getRepository(Fichier::class);
        $fichier = $repoFichier->find($id) ; 
           
        $em = $this->getDoctrine()->getManager(); // On récupère le gestionnaire des entités
        $em->remove($fichier); // Nous enregistrons notre nouveau thème
        $em->flush(); // Nous validons notre ajout
        $this->addFlash('notice', 'Fichier supprimé avec succés'); // Nous préparons le message à afficher à l’utilisateur sur la page où il se rendra
            
        
        return $this->redirectToRoute('liste-fichier'
            );
          
    }



     /**
     * @Route("/telechargement-fichier/{id}", name="telechargement-fichier" ,requirements={"id"="\d+"})
     */
  
    public function telechargementFichier(int $id, Request $request): Response
    {

        $em = $this->getDoctrine();
        $repoFichier = $em->getRepository(Fichier::class);
        $fichier = $repoFichier->find($id) ;  
        if($fichier!=null){

            return $this->file($this->getParameter('file_directory').'/'.$fichier->getNom(),$fichier->getvraiNom());
            }else{
                return $this->redirectToRoute('liste-fichier');

            }
          
    }

    /**
     * @Route("/liste-fichier", name="liste-fichier")
     */
  
    public function ListeFichier(Request $request): Response
    {

        $em = $this->getDoctrine();
        $repoFichier = $em->getRepository(Fichier::class);
        $fichiers = $repoFichier->findBy(array(),array('vraiNom'=>'ASC'));

        if ($request->get('supp')!=null){
            $fichier = $repoFichier->find($request->get('supp'));
            if($theme!=null){
                $em->getManager()->remove($fichier);
                $em->getManager()->flush();
            }
            return $this->redirectToRoute('liste-fichier');
        }
        return $this->render('fichier/liste-fichier.html.twig', ['fichiers'=>$fichiers // Nous passons la liste des thèmes à la vue
 ]);
 }







    /**
     * @Route("/ajout-fichier", name="ajout-fichier")
     */
    public function ajoutFichier(Request $request): Response
    {

        $fichier = new Fichier();
        $form = $this->createForm(AjoutFichierType::class,$fichier);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $file = $fichier->getNom(); // Récupère le fichier envoyé
                $fichier->setDate(new \DateTime()); //récupère la date du jour
                $fichier->setExtension($file->guessExtension()); // Récupère l’extension du fichier
                $fichier->setTaille($file->getSize()); // getSize contient la taille du fichier envoyé
                $fichier->setVraiNom($file->getClientOriginalName());
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                $fichier->setNom($fileName); // Le nom du fichier va être celui généré aléatoirement
                $em->persist($fichier); // Enregistrement du fichier dans la table
                $em->flush();
                try{
                    $file->move($this->getParameter('file_directory'),$fileName); // Nousdéplaçons le fichier dans le répertoire configuré dans services.yaml
                    $this->addFlash('notice', 'Fichier inséré');
                } 
                catch (FileException $e) { // erreur durant l’upload }
                    $this->addFlash('notice', 'Fichier non inséré, Veuillez Réesayer');
                }
            }
        }
        return $this->render('fichier/ajout-fichier.html.twig', [
        'form'=>$form->createView()
        ]);
    }

    /**
    * * @return string
    *
    * */

    private function generateUniqueFileName()
    {
        return md5(uniqid()); // Génère un md5 sur un identifiant généré aléatoirement
    }



}
