<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
	 * @IsGranted("ROLE_USER")
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * @Route("/favoris", name="ajax_get_favoris", methods={"GET"})
     * @return  JsonResponse
     */
    public function getFavoris(): JsonResponse
    {
        $response   = new JsonResponse('Une erreur est survenue !', 500);
        $response->headers->set('Content-Type', 'application/json');
        if($this->getUser()) {
            $favIds = [];
            foreach($this->getUser()->getFavoris() as $fav) {
                $favIds[] = $fav->getId(); 
            }
            $response = new JsonResponse(['ids' =>  $favIds], 200);
        }
        return $response;
    }

    /**
     * @Route("/favoris/new", name="ajax_new_favoris", methods={"POST"})
     */
    public function newFavoris(Request $request, ArticleRepository $articleRepository): JsonResponse
    {
        $response   = new JsonResponse('Une erreur est survenue !', 500);
        $response->headers->set('Content-Type', 'application/json');
        if($this->getUser()) {
            $article = $articleRepository->findOneBy(['id' => $request->request->get('articleId')]);
            $this->getUser()->addFavori($article);
            $this->getDoctrine()->getManager()->flush();
            $response = new JsonResponse('La favori a bien été ajouté !', 200);
        }
        return $response;
    }
}
