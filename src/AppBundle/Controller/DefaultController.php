<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use AppBundle\Service\Hydrator;
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
     */
    public function indexAction(TokenGeneratorInterface $generator)
    {
        $csrf_token = $generator->generateToken();
        $this->get('session')->set('csrf_token', $csrf_token);

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'csrf_token' => $csrf_token
        ]);
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function articleAction(ObjectManager $manager)
    {
        $articles = $manager->getRepository(Article::class)->findAll();

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler(function($object) {
            return $object->getId();
        });

        $serializer = new Serializer([$normalizer], [$encoder]);
        $jsonArticles = $serializer->serialize($articles, 'json');

        $response = new Response($jsonArticles);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/admin/article", name="create_article")
     * @param Request $request
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function createArticleAction(Request $request, ObjectManager $manager)
    {
        if ($request->isXmlHttpRequest()) {

            $article = new Article();

            foreach ($request->request as $field => $value) {
                if (method_exists($article, $method = 'set'. ucfirst($field))) {
                    $article->$method(htmlspecialchars($value));
                } else {
                    return new JsonResponse("Invalid Method name.");
                }
            }

            $article->setDate(new \DateTime());

            $file = $request->files->get('image');
            $filename = uniqid() . '.' . $file->guessExtension();

            $image = new Image();
            $image->setSrc($filename);
            $image->setTitle($article->getTitle());
            $image->setArticle($article);
            $article->addImage($image);

            $file->move($this->getParameter('images_directory'), $filename);

            $manager->persist($article);
            $manager->flush();

            return new JsonResponse("created");
        }
    }

    /**
     * @Route("/login", name="login")
     * @return JsonResponse|Response
     */
    public function loginAction(ObjectManager $manager, Request $request, Hydrator $hydrator)
    {
        if ($request->isXmlHttpRequest()) {
            if ($hydrator->isFormValid(User::class)) {

                $user = $manager->getRepository(User::class)->findOneBy([
                    'username' => $request->request->get('username')
                ]);

                if (is_null($user)) {
                    return new JsonResponse("Le nom d'utilisateur n'existe pas.");
                }

                if (password_verify($request->request->get('password'), $user->getPassword())) {
                    $token = hash('sha256', time() . $user->getUsername());

                    $this->get('session')->set('token', $token);

                    return new JsonResponse(["token" => $token]);
                }

                return new JsonResponse("Mot de passe incorrect.");
            }

            return new JsonResponse('Formulaire invalide.');
        }

        return new Response("Requête non autorisée", Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/checkToken", name="check_token")
     * @return JsonResponse|Response
     */
    public function checkTokenAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($this->get("session")->get("token") === $request->get('token'));
        }

        return new Response("Requête non autorisée", Response::HTTP_BAD_REQUEST);
    }
}
