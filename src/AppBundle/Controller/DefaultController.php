<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Image;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function articleAction(ObjectManager $manager)
    {
        $articles = $manager->getRepository(Article::class)->findAll();

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

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
     * @return Response
     */
    public function createArticleAction(Request $request, ObjectManager $manager)
    {
        if ($request->isXmlHttpRequest()) {

            $article = new Article();

            foreach ($request->request as $field => $value) {
                $method = 'set'. ucfirst($field);
                $article->$method(htmlspecialchars($value));
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

            return new Response("created");
        }
    }
}
