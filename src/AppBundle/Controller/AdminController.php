<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Image;
use AppBundle\Service\Hydrator;
use AppBundle\Service\MetaService;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * @Route("/admin/create", name="create_article")
     * @param Request $request
     * @param Hydrator $hydrator
     * @param MetaService $metaService
     * @return JsonResponse
     */
    public function createArticleAction(Request $request, Hydrator $hydrator, MetaService $metaService)
    {
        if ($request->isXmlHttpRequest()) {

            if ($hydrator->isFormValid([Article::class, Image::class])) {
                $object = $hydrator->hydrateObject(Article::class);
                $metaService->persistAndFlush([$object]);
                return new JsonResponse("Création réussie.", Response::HTTP_CREATED);
            } else {
                return new JsonResponse('Formulaire invalide');
            }
        } else {
            return new JsonResponse("Requête invalide", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/admin/articles/delete/{id}", name="delete_article")
     * @param int $id
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function deleteArticleAction($id, ObjectManager $manager)
    {
        $ret = $this->getArticle($id);

        if ($ret instanceof Article) {
            $manager->remove($ret);
            $manager->flush();
            return new JsonResponse("L'article a été supprimé.");
        } else {
            return $ret;
        }
    }

    /**
     * @Route("/admin/articles/edit/{id}", name="edit_article")
     * @param int $id
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function editArticleAction($id, Request $request, Hydrator $hydrator)
    {
        if ($request->isXmlHttpRequest()) {
            if ($hydrator->isFormValid([Article::class, Image::class])) {
                $ret = $this->getArticle($id);

                if ($ret instanceof Article) {
                    $hydrator->updateObject($ret);
                    return new JsonResponse("L'article a été mis à jour.");
                } else {
                    return $ret;
                }
            } else {
                return new JsonResponse('Formulaire invalide');
            }
        } else {
            return $this->get('app.serializor')->serialize($this->getArticle($id));
        }
    }

    /**
     * @param int $id
     * @return Article|null|object|JsonResponse
     */
    private function getArticle($id)
    {
        if(!is_null($article = $this->getDoctrine()->getRepository(Article::class)->find($id))) {
            return $article;
        } else {
            return new JsonResponse("L'article n'existe pas.", Response::HTTP_BAD_REQUEST);
        }
    }
}