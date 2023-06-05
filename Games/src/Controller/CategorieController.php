<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use App\Entity\Article;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\CategorieType;


class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {$entityManager = $this->getDoctrine()->getManager();
        $categorie = new Categorie();
        $article = new Article();
        $article -> setCategorie($categorie);
        $entityManager->persist($categorie);
        $entityManager->persist($article);
        $entityManager->flush();
        return $this->render('categorie/index.html.twig', [
            'categorie' => $categorie,
        ]);
    }
    /**
* @Route("/categorie/{id}", name="categorie_show")
*/
public function show($id, Request $request )
{
 $categorie = $this->getDoctrine()
 ->getRepository(Categorie::class)
 ->find($id);
 $em=$this-> getDoctrine()->getManager();
 $lescategories=$em->getRepository(Categorie::class)
 ->findBy(['id'=>$id]);
 $publicPath = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/Categories/';
       
 if (!$categorie) {
 throw $this->createNotFoundException(
 'No categorie found for id '.$id
 );
 }
 return $this->render('categorie/show.html.twig',
  [  'lescategories' => $lescategories, 
    'categorie' =>$categorie,
     'publicPath' => $publicPath
 ]);
 }
  /**
* @Route("/Ajouter", name ="Ajouter")
*/
public function ajouter(Request $request)
{
    $categorie = new Categorie();
    $form = $this->createFormBuilder($categorie)
        ->add('nomCategorie', TextType::class)
        ->add('Valider', SubmitType::class, [
            'label' => 'Valider',
            'attr' => [
                'class' => 'btn btn-primary', 
            ],
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($categorie);
        $em->flush();

        return $this->redirectToRoute('home');
    }
   
    return $this->render('categorie/ajouter.html.twig', [
        'form' => $form->createView(),
    ]);
    
}
/** 
* @Route("/supp/{id}", name="categorie_delete")
*/
public function delete ($id): Response
{
    $c= $this->getDoctrine()
       ->getRepository(Categorie::class)
       ->find($id);
    if (!$c){
        throw $this->createNotFoundException(
              'No categorie found for id '.$id
        );
    }
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($c);

    $entityManager->flush();
    return $this->redirectToRoute('home');
}

/**
* @Route("/editU/{id}", name="edit_categorie")
* Method({"GET","POST"})
*/
public function edit(Request $request, $id)
{ 
$categorie = $this->getDoctrine()
->getRepository(Categorie::class)
->find($id);

if (!$categorie){
throw $this->createNotFoundException(
'No categorie found for id '.$id
);
}
$form = $this->createFormBuilder($categorie)
->add('nomCategorie', TextType::class, [
    'required' => false,
    'data_class' => null,
])
->add('save', SubmitType::class, ['label' => 'Valider',
'attr' => [
    'class' => 'btn btn-primary', 
],
])
->getForm();

$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
$entityManager = $this->getDoctrine()->getManager();
$entityManager->flush();

return $this->redirectToRoute('home', ['id' => $categorie->getId()]);
}

return $this->render('categorie/ajouter.html.twig', [
'form' => $form->createView(),
'categorie' => $categorie,
]);
}



 /**
     * @Route("/listecategorie", name="liste_categorie")
     */
    public function afficherList(Request $request)
    {
    $form = $this->createFormBuilder()
    ->add('critere', TextType::class, [
        'label' => 'Critere',
        'attr' => [
            'class' => 'form-control', 
        ],
    ])
    ->add('valider', SubmitType::class, [
        'label' => 'Valider',
        'attr' => [
            'class' => 'btn btn-primary', 
        ],
    ])
    ->getForm();
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Categorie::class);
        $lesCategories = $repo->findAll();
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $critere = $data['critere']; 
            $lesCategories = $repo->findBy(['nomCategorie' => $critere]);
        }



        return $this->render('categorie/liste.html.twig', [
            'lesCategories' => $lesCategories,
            'form' => $form->createView()
        ]);
    }
}

