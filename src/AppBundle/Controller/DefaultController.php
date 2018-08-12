<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Legal;
use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Statistic;
use AppBundle\Service\DataSaver;
use AppBundle\Service\Hydrator;
use AppBundle\Service\MetaService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

        return new JsonResponse($articles);
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
    * @Route("/pullIn/article/{slug}", name="pull_in")
    * @param Request $request
    * @param ObjectManager $manager
    * @return \Symfony\Component\HttpFoundation\RedirectResponse
    */
    public function pullInAction($slug, Request $request, ObjectManager $manager)
    {
        $url = $_SERVER['HTTP_HOST'] . "/#/" . "article/$slug";

        if (!preg_match("/^(http|https):\/\//", $url)) {
            $url = 'http://' . $url;
        }

        $statistic = (new Statistic())
            ->setData(substr($request->getRequestUri(), 7))
            ->setType('newsletter')
            ->setBot(false);

        $manager->persist($statistic);
        $manager->flush();

        return new RedirectResponse($url);
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
        return new JsonResponse($manager->getRepository(Category::class)->myFindArticleByCategory($category));
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
     * @return Response
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
            }

            return new JsonResponse('Formulaire invalide');
        }

        return new JsonResponse('Requête non valide', Response::HTTP_BAD_REQUEST);
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
                $newsletter = $hydrator->hydrateObject(Newsletter::class);
                $metaService->persistAndFlush([$newsletter]);
                return new JsonResponse('Votre abonnement a bien été pris en compte.');
            }

            return new JsonResponse("Vous êtes déjà abonné(e).");
        }

        return new JsonResponse('Formulaire invalide');
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
    public function statAction(Request $request, DataSaver $dataSaver)
    {
        $dataSaver->registerData(
            $data = htmlspecialchars($request->get("data")),
            $type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        );

        return new Response($data);
    }

    /**
     * @Route("/legal", name="legal_mentions")
     * @param ObjectManager $manager
     * @return Response
     */
    public function legalAction(ObjectManager $manager)
    {
        if (!empty($legal = $manager->getRepository(Legal::class)->findOneBy([]))) {
            return $this->getJson($legal);
        }

        return new Response();
    }

    /**
     * @Route("/unsubscribe/{token}", name="user_unsubscribe")
     */
    public function unsubscribeAction($token, ObjectManager $manager)
    {
        $user = $manager->getRepository(Newsletter::class)->findOneBy([
            "token" => $token
        ]);

        if (!is_null($user)) {

            $manager->remove($user);
            $manager->flush();

            return new JsonResponse("Votre désabonnement a bien été pris en compte.");
        }

        return new JsonResponse("L'utilisateur n'existe pas !");
    }

    /**
     * @Route("/contact", name="user_contact")
     *
     */
    public function contactAction(Request $request, Hydrator $hydrator, MetaService $metaService)
    {
        if ($request->isXmlHttpRequest()) {
            if ($hydrator->isFormValid([Contact::class], $request->get('sender'))) {
                $contact = $hydrator->hydrateObject(Contact::class);
                $metaService->persistAndFlush([$contact]);

                return new JsonResponse("Votre demande a bien été envoyée.", Response::HTTP_CREATED);
            }
        }

        return new Response("Méthode non autorisée.", Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
