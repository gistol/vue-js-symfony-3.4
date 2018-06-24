<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Newsletter;
use AppBundle\Service\AppTools;
use AppBundle\Service\DataSaver;
use AppBundle\Service\Hydrator;
use AppBundle\Service\MetaService;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * Function to return a specific csrf_token for each form present in components
     *
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
     * Function to return the number of articles in db
     *
     * @Route("/articlesCount", name="articles_count")
     */
    public function getNbArticlesAction(ObjectManager $manager)
    {
        $result = $manager->getRepository(Article::class)->getNumberOfArticles();
        return new Response($result["nb_articles"]);
    }

    /**
     * Function to fetch articles in db by slices of 9 or less (last request)
     *
     * @Route("/articles/{id}", name="articles")
     */
    public function getArticlesAction(ObjectManager $manager, $id)
    {
        $articles = $manager->getRepository(Article::class)->myFindBy($id);

        return $this->getJson($articles);
    }

    /**
     * Function to return a target article
     *
     * @Route("/article/{slug}", name="article")
     * @param string $slug
     * @return Response
     */
    public function getArticleAction($slug, ObjectManager $manager)
    {
        return $this->getJson($manager->getRepository(Article::class)->findOneBy(["slug" => $slug]));
    }

    /**
     * Function to find articles linked to a target category
     *
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
     * Function to add a user comment to a target article
     *
     * @Route("/article/{slug}/comment", name="user_comment")
     * @param string $slug
     * @param Request $request
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
     * Function to add a new user to the newsletter
     *
     * @Route("/newsletter", name="user_newsletter")
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
     * Function to return all categories in db
     *
     * @Route("/categories", name="user_categories")
     * @param ObjectManager $manager
     * @return Response
     */
    public function categoriesAction(ObjectManager $manager)
    {
        return $this->get('app.serializor')->serialize($manager->getRepository(Category::class)->myFindAllCategories());
    }

    /**
     * Function to get Articles by keyword
     *
     * @Route("/search", name="user_search")
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function searchAction(Request $request, ObjectManager $manager)
    {
        return !empty($article = $manager->getRepository(Article::class)->findOneBy(["title" => $search = $request->get('search')]))
            ? $this->getJson($article)
            : $this->getJson($manager->getRepository(Article::class)->myFindByKeyword($search));
    }

    /**
     * @Route("/statistics", name="user_stat")
     */
    public function statAction(DataSaver $dataSaver)
    {
        $dataSaver->registerData(
            $data = filter_input(INPUT_POST, "data", FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            $type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        );

        return new Response($data);
    }
}
