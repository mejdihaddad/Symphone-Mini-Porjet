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


class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {  
        return $this->render('article/index.html.twig', [
        'controller_name' => 'ArticleController',
        ]);
    }
    /**
 * @Route("/", name="home")
 */
public function home(Request $request)
{
    $form = $this->createFormBuilder()
    ->add('critere', TextType::class, [
        'label' => 'Critere',
        'attr' => [
            'class' => 'form-control', 
        ],
    ])
    ->add('valider', SubmitType::class, [
        'label' => 'Search',
        'attr' => [
            'class' => 'btn btn-primary', 
        ],
    ])
    ->getForm();

$form->handleRequest($request);

$entityManager = $this->getDoctrine()->getManager();
$repo = $entityManager->getRepository(Article::class);

$lesArticles = $repo->findAll();

if ($form->isSubmitted() && $form->isValid()) {
   $data = $form->getData();
   $critere = $data['critere'];
   $lesArticles = $repo->findBy(['Libelle' => $critere]);
}

return $this->render(
   'article/home.html.twig',
   ['lesarticles' => $lesArticles, 'form' => $form->createView()]
);
}

    /**
     * @Route("/listearticle", name="liste_article")
     */
    public function afficherList(Request $request)
    {
        $form = $this->createFormBuilder()
        ->add('critere', TextType::class)
        ->add('valider', SubmitType::class)
        ->getForm();
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Article::class); 
        $lesArticles = $repo->findAll(); 

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $critere = $data['critere']; 
            $lesArticles = $repo->findBy(['Libelle' => $critere]);
        }

        return $this->render('article/liste.html.twig', [ 
            'lesArticles' => $lesArticles, 
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/AjouterArticle", name="AjouterArticle")
     */
    public function ajouterArticle(Request $request)
    {
        $article = new Article();
        $form = $this->createFormBuilder($article)
            
            ->add('Libelle', TextType::class)
            ->add('is_disponible', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
            ])
            ->add('price', NumberType::class, [
                'scale' => 2,
            ])
            ->add('Marque', TextType::class)
            ->add('Categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie',
            ])
            ->add('Valider', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary', 
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('liste_article');
        }

        return $this->render('article/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
  /**
 * @Route("/article/delete/{id}", name="article_delete")
 */
public function delete($id): Response
{
    $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

    if (!$article) {
        throw $this->createNotFoundException('No article found for this id: ' . $id);
    }

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($article);
    $entityManager->flush();

    $this->addFlash('notice', 'Article supprimé avec succès.');

    return $this->redirectToRoute('liste_article');
}
/**
 * @Route("/editArticle/{id}", name="edit_article")
 * Method({"GET","POST"})
 */
public function editArticle(Request $request, $id)
{
    $entityManager = $this->getDoctrine()->getManager();
    $article = $entityManager->getRepository(Article::class)->find($id);

    if (!$article) {
        throw $this->createNotFoundException(
            'No article found for id '.$id
        );
    }

    $form  = $this->createFormBuilder($article)
        ->add('Libelle', TextType::class)
        ->add('is_disponible', CheckboxType::class, [
            'label' => 'Disponible',
            'required' => false,
        ])
        ->add('price', NumberType::class, [
            'scale' => 2,
        ])
        ->add('Marque', TextType::class)
        ->add('Categorie', EntityType::class, [
            'class' => Categorie::class,
            'choice_label' => 'nomCategorie',
        ])
        ->add('Valider', SubmitType::class, ['label' => 'Valider',
        'attr' => [
            'class' => 'btn btn-primary', 
        ],
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }

    return $this->render('article/ajouter.html.twig', [
        'form' => $form->createView()
    ]);
}

/**
 * @Route("/article/{id}", name="article_show")
 */
public function show($id, Request $request)
{
    $article = $this->getDoctrine()
        ->getRepository(Article::class)
        ->find($id);

    if (!$article) {
        throw $this->createNotFoundException(
            'No article found for id ' . $id
        );
    }

    return $this->render('article/show.html.twig', [
        'article' => $article,
    ]);
}

    
    
}
