<?php

namespace AppBundle\Service;

use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class MetaService
 * @package AppBundle\Service
 */
class MetaService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var FileUploader $fileUploader
     */
    protected $fileUploader;

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @var Session $session,
     */
    protected $session;

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * MetaService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, FileUploader $fileUploader, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->fileUploader = $fileUploader;
        $this->logger = $logger;
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
     */
    public function err($msg, array $context = [])
    {
        $this->logger->error($msg, [$context]);
    }

    /**
     * @param $msg
     * @param array $context
     */
    public function info($msg, array $context = [])
    {
        $this->logger->info($msg, $context);
    }

    /**
     * @param $msg
     * @param array $context
     */
    public function crit($msg, array $context = [])
    {
        $this->logger->critical($msg, $context);
    }

    /**
     * @param $msg
     * @param array $context
     */
    public function warn($msg, array $context = [])
    {
        $this->logger->warning($msg, $context);
    }

    /**
     * @return \Doctrine\ORM\EntityManager|object
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param $entity
     */
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
        }

        $this->flush();
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

    /**
     * @return FileUploader
     */
    public function getFileUploader()
    {
        return $this->fileUploader;
    }
}