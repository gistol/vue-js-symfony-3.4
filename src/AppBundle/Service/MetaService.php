<?php

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

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

    /**
     * @var Session $session,
     */
    protected $session;

    /**
     * MetaService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return null|Request
     */
    public function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

    /**
     * @return mixed|Session
     */
    public function getSession()
    {
        return $this->container->get('session');
    }

    public function err($msg, array $context)
    {
        return $this->container->get('logger')->err($msg, $context);
    }

    public function info($msg, array $context)
    {
        return $this->container->get('logger')->info($msg, $context);
    }

    public function crit($msg, array $context)
    {
        return $this->container->get('logger')->crit($msg, $context);
    }

    public function warn($msg, array $context)
    {
        return $this->container->get('logger')->warn($msg, $context);
    }
}