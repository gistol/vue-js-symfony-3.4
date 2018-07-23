<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\Hydrator;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param  ObjectManager $manager
     * @param Request $manager
     * @param Hydrator $hydrator
     * @return JsonResponse|Response
     */
    public function loginAction(ObjectManager $manager, Request $request, Hydrator $hydrator)
    {
        if ($request->isXmlHttpRequest()) {

            if ($hydrator->isFormValid([User::class], $request->get('sender'))) {

                $user = $manager->getRepository(User::class)->findOneBy([
                    'username' => $request->get('username')
                ]);

                if (null === $user) {
                    return new JsonResponse("Le nom d'utilisateur n'existe pas.");
                }

                if (password_verify($request->get('password'), $user->getPassword())) {
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
     * @Route("/getSessionToken", name="get_token")
     * @return Response|JsonResponse
     */
    public function checkTokenAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response("Requête non autorisée.", Response::HTTP_BAD_REQUEST);
        }

        if (is_null($token = $request->getSession()->get('token'))) {

            $msg = "Le token n'existe pas en session. L'authentification a échoué.";
            $this->get('logger')->crit($msg);
            return new JsonResponse(["err" => $msg]);
        }

        return new JsonResponse($token);
    }
}