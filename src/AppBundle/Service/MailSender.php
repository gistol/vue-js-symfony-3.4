<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use Symfony\Component\Templating\EngineInterface;

class MailSender
{
    /**
     * @var \Swift_Mailer $mailer
     */
    private $mailer;

    /**
     * @var \Swift_Message $message
     */
    private $message;

    /**
     * @var EngineInterface $templating
     */
    private $templating;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $engine)
    {
        $this->mailer =$mailer;
        $this->message = new \Swift_Message();
        $this->templating = $engine;
    }

    public function send(Article $article, $subscribers, $sender)
    {
        $this->message->setSubject($article->getTitle())
            ->setTo(array_values($subscribers))
            ->setFrom($sender)
            ->setBody(
                $this->templating->render('default/newsletter.html.twig', [
                "article" => $article
            ]), "UTF-8");

        $this->mailer->send($this->message);
    }
}