<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Image;
use AppBundle\Service\MetaService;
use Doctrine\ORM\Event\LifecycleEventArgs;

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
            $this->metaService->info("Removed file " . $file);
            $this->metaService->getFileUploader()->removeFile($file);
        }
    }
}