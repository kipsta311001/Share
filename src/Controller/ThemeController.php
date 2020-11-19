<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ThemeType;
use App\Form\ModifThemeType;
use App\Entity\Theme;



class ThemeController extends AbstractController
{
    /**
     * @Route("/theme/ajout-theme", name="ajout-theme")
     */
  
    public function AjoutTheme(Request $request): Response
    {

        $theme = new Theme(); // Instanciation d’un objet Theme
        $form = $this->createForm(ThemeType::class,$theme); // Création du formulaire pour ajouter un thème, en lui donnant l’instance.
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) { 
                $em = $this->getDoctrine()->getManager(); // On récupère le gestionnaire des entités
                $em->persist($theme); // Nous enregistrons notre nouveau thème
                $em->flush(); // Nous validons notre ajout
                $this->addFlash('notice', 'Thème inséré'); // Nous préparons le message à afficher à l’utilisateur sur la page où il se rendra
            }
            return $this->redirectToRoute('ajout-theme'); // Nous redirigeons l’utilisateur sur l’ajout d’un thème après l’insertion.
        }
        return $this->render('theme/ajout_theme.html.twig', ['form'=>$form->createView() // Nous passons le formulaire à la vue
 ]);
 }

 /**
     * @Route("/theme/liste-theme", name="liste-theme")
     */
  
    public function ListeThemes(Request $request): Response
    {

        $em = $this->getDoctrine();
        $repoTheme = $em->getRepository(Theme::class);
        $themes = $repoTheme->findBy(array(),array('libelle'=>'ASC'));

    

            
        return $this->render('theme/liste-themes.html.twig', ['themes'=>$themes // Nous passons la liste des thèmes à la vue
 ]);
 }

  /**
     * @Route("/theme/modif_theme/{id}", name="modif_theme" ,requirements={"id"="\d+"})
     */
  
    public function ModifTheme(int $id, Request $request): Response
    {

        $em = $this->getDoctrine();
        $repoTheme = $em->getRepository(Theme::class);

        $theme = $repoTheme->find($id) ;  
        
        if($theme==null){
            $this->addFlash('notice', "Ce thème n'existe pas");
            return $this->redirectToRoute('liste-theme');
            }
            
            // Instanciation d’un objet Theme avec son id
        $form = $this->createForm(ModifThemeType::class,$theme); // Création du formulaire pour ajouter un thème, en lui donnant l’instance.
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) { 
                $em = $this->getDoctrine()->getManager(); // On récupère le gestionnaire des entités
                $em->persist($theme); // Nous enregistrons notre nouveau thème
                $em->flush(); // Nous validons notre ajout
                $this->addFlash('notice', 'Thème Modifié avec succés'); // Nous préparons le message à afficher à l’utilisateur sur la page où il se rendra
            }
            return $this->redirectToRoute('liste-theme'); // Nous redirigeons l’utilisateur sur l’ajout d’un thème après l’insertion.
        }
        return $this->render('theme/modif_theme.html.twig', ['form'=>$form->createView() // Nous passons le formulaire à la vue
 ]);
 }
 
}
