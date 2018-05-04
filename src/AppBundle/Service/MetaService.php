<?php

namespace AppBundle\Service;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class MetaService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Request $request
     */
    protected $request;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $container->get('request_stack')->getCurrentRequest();
    }
}