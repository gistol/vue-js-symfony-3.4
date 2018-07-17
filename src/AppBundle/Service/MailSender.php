<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Templating\EngineInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MailSender
{
    /**
     * @var \Swift_Mailer $mailer
     */
    private $mailer;

    /**
     * @var EngineInterface $templating
     */
    private $templating;

    /**
     * MailSender constructor.
     * @param \Swift_Mailer $mailer
     * @param EngineInterface $engine
     */
    public function __construct(\Swift_Mailer $mailer, EngineInterface $engine)
    {
        $this->mailer =$mailer;
        $this->templating = $engine;
    }

    public function send(Article $article, $subscribers, $sender, $targetDir, Router $router, Request $request)
    {
        $httpReferer = $_SERVER['HTTP_REFERER'];
        $articleUrl = $router->generate('article', ['slug' => $article->getSlug()], UrlGeneratorInterface::ABSOLUTE_PATH);

        $articleUrl = $httpReferer . "#" . $articleUrl;

        foreach ($subscribers as $subscriber) {
            $unsubscribeUrl = $router->generate('user_unsubscribe', ['token' => $subscriber['token']], UrlGeneratorInterface::ABSOLUTE_URL);

            $message = new \Swift_Message();
            $src = $message->embed(\Swift_Image::fromPath($targetDir . '/' . $article->getImages()[0]->getSrc()));
            $message->setSubject("Nouvel article publié : " . $article->getTitle())
                ->setTo($subscriber['email'])
                ->setFrom($sender)
                ->setBody(
                    $this->templating->render('default/newsletter.html.twig', [
                        "article" => $article,
                        "src" => $src,
                        "articleUrl" => $articleUrl,
                        "unsubscribeUrl" => $unsubscribeUrl
                    ]), "text/html", "UTF-8");

            $this->mailer->send($message);
        }
    }
}