<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Image;
use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Statistic;
use AppBundle\Service\DataSaver;
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

            if ($hydrator->isFormValid([Article::class, Image::class, Category::class], $request->get('sender'))) {
                $article = $hydrator->hydrateObject(Article::class);
                $metaService->persistAndFlush([$article]);

                if ("on" === $request->get('newsletter')) {
                    $this->get('app.mailer')
                         ->send(
                             $article,
                             $metaService->getEntityManager()->getRepository(Newsletter::class)->myFindAll()[0],
                             $this->getParameter('mailer_user')
                         );
                }

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
            if ($hydrator->isFormValid([Article::class, Image::class, Category::class], $request->get('sender'))) {
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
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     * @Route("/admin/comment/status", name="comment_status")
     */
    public function changeCommentStatus(Request $request, ObjectManager $manager)
    {
        $comment = $manager->getRepository(Comment::class)->findOneBy([
            "id" => $request->get('id')
        ]);

        $comment->setPublished("true" === $request->get('published'));

        $manager->flush();

        return new JsonResponse("Mise à jour réussie");
    }

    /**
     * @Route("/admin/comments", name="admin_comments")
     * @param ObjectManager $manager
     * @return Response
     */
    public function commentAction(ObjectManager $manager)
    {
        return $this->get('app.serializor')->serialize($manager->getRepository(Comment::class)->findAll());
    }

    /**
     * @Route("/admin/newsletters", name="admin_newsletters")
     * @param ObjectManager $manager
     * @return Response
     */
    public function newsletterAction(ObjectManager $manager)
    {
        return $this->get('app.serializor')->serialize($manager->getRepository(Newsletter::class)->findAll());
    }

    /**
     * @Route("/admin/statistics", name="admin_statistics")
     */
    public function getStatAction(Request $request, ObjectManager $manager, DataSaver $dataSaver)
    {
        if ($request->get("csrf_token") === $request->getSession()->get("statistic")) {

            $type = $request->get("type");
            $bot = $request->get("bot");
            $start = $request->get("start");
            $end = $request->get("end");

            $statistics = $manager->getRepository(Statistic::class)
                ->myFindBy($type, $bot, $start, $end);

            return new Response(json_encode($dataSaver->getStatistics($statistics)));
        } else {
            return new JsonResponse("Formulaire invalide.");
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