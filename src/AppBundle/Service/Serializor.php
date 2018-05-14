<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Serializor
{
    public function serialize($arg)
    {
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler(function($object) {
            return $object->getId();
        });

        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($arg, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}