<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

class AppTools
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * AppTools constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $slug
     * @return string
     */
    public function slugify($slug)
    {
        $slug = htmlspecialchars(trim($slug));
        $slug = str_replace(["à", "â", "ä"], 'a', $slug);
        $slug = str_replace(["ö", "ô"], 'o', $slug);
        $slug = str_replace(["é", "è", "ê", "ë"], 'e', $slug);
        $slug = str_replace(["û", "ü", "ù"], 'u', $slug);
        $slug = str_replace(["ï", "î"], 'i', $slug);
        $slug = preg_replace('/\s|\'/', '-', $slug);

        return $this->checkForSimilarSlug(strtolower($slug));
    }

    public function checkForSimilarSlug($slug)
    {
        $slugs = $this->em->getRepository(Article::class)->myFindBySlug($slug);
        $val = intval($slugs[0]['count_same_slug']);
        return $val > 0 ?
            $slug . '-' . $val+=1 : $slug;
    }
}