<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Newsletter;
use AppBundle\Service\Hydrator;
use AppBundle\Service\MetaService;
use AppBundle\Service\Serializor;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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
    public function getCsrfToken(TokenGeneratorInterface $generator, Request $request)
    {
        $csrf_token = $generator->generateToken();
        $param = $request->request->get('sender');
        $this->get('session')->set($param, $csrf_token);

        return new JsonResponse([
            'csrf_token' => $csrf_token
        ]);
    }

    /**
     * @Route("/articlesCount", name="articles_count")
     */
    public function getNbArticlesAction(ObjectManager $manager)
    {
        $result = $manager->getRepository(Article::class)->getNumberOfArticles();
        return new Response($result["nb_articles"]);
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
     * @Route("/category/{category}", name="category")
     * @param string $category
     * @return Response
     */
    public function getArticlesByCategory($category, ObjectManager $manager)
    {
        return $this->getJson($manager->getRepository(Category::class)->findOneBy(['category' => $category])->getArticles());
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
            if ($hydrator->isFormValid([Comment::class], $request->get('sender'))) {
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

    /**
     * @Route("/newsletter", name="newsletter")
     */
    public function handleNewsletterAction(Hydrator $hydrator, Request $request, MetaService $metaService)
    {
        if ($hydrator->isFormValid([Newsletter::class], $request->get('sender'))) {

            $email = htmlspecialchars($request->request->get('email'));

            if (is_null($this->getDoctrine()->getRepository(Newsletter::class)->findOneBy(["email" => $email]))) {
                $newsletter = (new Newsletter())->setEmail($email);
                $metaService->persistAndFlush([$newsletter]);
                return new JsonResponse('Votre abonnement a bien été pris en compte.');
            }

            return new JsonResponse("Vous êtes déjà abonné(e).");
        } else {
            return new JsonResponse('Formulaire invalide');
        }
    }

    /**
     * @Route("/categories", name="categories")
     * @param ObjectManager $manager
     * @return Response
     */
    public function categoriesAction(ObjectManager $manager)
    {
        return $this->get('app.serializor')->serialize($manager->getRepository(Category::class)->findAll());
    }
}
