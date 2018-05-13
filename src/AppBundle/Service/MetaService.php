<?php

namespace AppBundle\Service;

use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Config\Tests\Fixtures\Configuration\ExampleConfiguration;
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

    /**
     * @param $msg
     * @param array $context
     * @return bool
     */
    public function err($msg, array $context = null)
    {
        return $this->container->get('logger')->err($msg, $context);
    }

    /**
     * @param $msg
     * @param array $context
     * @return bool
     */
    public function info($msg, array $context = null)
    {
        return $this->container->get('logger')->info($msg, $context);
    }

    /**
     * @param $msg
     * @param array $context
     * @return bool
     */
    public function crit($msg, array $context = null)
    {
        return $this->container->get('logger')->crit($msg, $context);
    }

    /**
     * @param $msg
     * @param array $context
     * @return bool
     */
    public function warn($msg, array $context = null)
    {
        return $this->container->get('logger')->warn($msg, $context);
    }

    public function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    public function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    public function flush()
    {
        try {
            $this->getEntityManager()->flush();
        } catch (OptimisticLockException $exception) {
            $this->warn($exception->getMessage(), ["MetaService" => "flush()"]);
        }

    }

    /**
     * @param array $entities
     */
    public function persistAndFlush(array $entities = [])
    {
        foreach ($entities as $entity) {
            if (is_object($entity)) {
                $this->persist($entity);
            }

            $this->flush();
        }
    }

    /**
     * @param string $param
     * @return string
     */
    public function getParameter($param)
    {
        try {
            $param = $this->container->getParameter($param);
        } catch (\Exception $exception) {
            $this->err("The parameter given does not exist");
        }

        return $param;
    }
}