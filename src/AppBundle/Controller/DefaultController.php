<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
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
     * @Route("/articles", name="articles")
     */
    public function getArticlesAction(ObjectManager $manager)
    {
        $articles = $manager->getRepository(Article::class)->findAll();

        return $this->getJson($articles);
    }

    /**
     * @param $arg
     * @return Response
     */
    private function getJson($arg)
    {
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler(function($object) {
            return $object->getId();
        });

        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($arg, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
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
}
