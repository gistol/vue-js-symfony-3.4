<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Image;
use AppBundle\Service\MetaService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class EntityListener
{
    /**
     * @var MetaService $metaService
     */
    protected $metaService;

    /**
     * EntityListener constructor.
     * @param MetaService $metaService
     */
    public function __construct(MetaService $metaService)
    {
        $this->metaService = $metaService;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Image) {
            $file = $entity->getSrc();
            $this->metaService->getFileUploader()->removeFile($file);
            $this->metaService->info("Removed file " . $file, ['EntityListener' => $entity->getArticle()->getTitle()]);
        }
    }
}