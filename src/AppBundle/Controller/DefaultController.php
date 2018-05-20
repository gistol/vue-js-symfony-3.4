<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Service\Hydrator;
use AppBundle\Service\MetaService;
use AppBundle\Service\Serializor;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction()
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/csrf_token", name="csrf_token")
     */
    public function getCsrfToken(TokenGeneratorInterface $generator)
    {
        $csrf_token = $generator->generateToken();
        $this->get('session')->set('csrf_token', $csrf_token);

        return new JsonResponse(['csrf_token' => $csrf_token]);
    }

    /**
     * @Route("/articles/{id}", name="articles")
     */
    public function getArticlesAction(ObjectManager $manager, $id)
    {
        $articles = $manager->getRepository(Article::class)->myFindBy($id);

        return $this->getJson($articles);
    }

    /**
     * @Route("/article/{slug}", name="article")
     * @param string $slug
     * @return Response
     */
    public function getArticleAction($slug, ObjectManager $manager)
    {
        return $this->getJson($manager->getRepository(Article::class)->findOneBy(["slug" => $slug]));
    }

    /**
     * @param $arg
     * @return Response
     */
    private function getJson($arg)
    {
        return $this->get('app.serializor')->serialize($arg);
    }

    /**
     * @Route("/article/{slug}/comment", name="handle_comment")
     * @param string $slug
     * @param Request $request
     * @param Hydrator $hydrator
     * @param MetaService $metaService
     * @return JsonResponse
     */
    public function handleCommentAction($slug, ObjectManager $manager, Request $request, Hydrator $hydrator, MetaService $metaService)
    {
        if (is_null($article = $manager->getRepository(Article::class)->findOneBy(['slug' => $slug]))) {
            return new JsonResponse("Article inconnu", Response::HTTP_NOT_FOUND);
        }

        if ($request->isXmlHttpRequest()) {
            if ($hydrator->isFormValid([Comment::class])) {
                $comment = $hydrator->hydrateObject(Comment::class);
                $article->addComment($comment);
                $comment->setArticle($article);
                $metaService->persistAndFlush([$comment]);
                return new JsonResponse("Votre commentaire a bien été envoyé !");
            } else {
                return new JsonResponse('Formulaire invalide');
            }
        } else {
            return new JsonResponse('Requête non valide', Response::HTTP_BAD_REQUEST);
        }
    }
}
