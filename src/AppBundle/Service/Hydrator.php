<?php

namespace AppBundle\Service;

use Symfony\Component\BrowserKit\Response;

class Hydrator extends MetaService
{
    public function isFormValid($class)
    {
        foreach ($this->request->request as $field => $value) {
            if ($field !== "csrf_token") {
                if (!method_exists(new $class, ($method = 'get' . ucfirst($field)))) {
                    return false;
                }
            }
        }

        return true;
    }
}