<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Image;

class Hydrator extends MetaService
{
    /**
     * @var \stdClass
     */
    private $class;

    /**
     * Function to check whether the form is conform or not
     * Added field in comparison to the specified entity will result in
     * returning false as well as a wrong csrf_token
     *
     * @param $class
     * @return bool
     */
    public function isFormValid($class)
    {
        $this->class = $class;

        foreach ($this->getRequest()->request as $field => $value) {

            if ($field === 'csrf_token') {
                if (!($value === $this->getSession()->get('csrf_token'))) {
                    return false;
                }
            } else {
                if (!method_exists(new $class, ($method = 'get' . ucfirst($field)))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Function to hydrate the specified object
     * Not need to validate fields as already done
     *
     * @param $class
     * @return object
     */
    public function hydrateObject()
    {
        $object = new $this->class;

        foreach ($this->getRequest()->request as $field => $value) {
            if ($field !== "csrf_token") {
                $method = 'set' . ucfirst($field);
                $object->$method(htmlspecialchars($value));
            }
        }

        if ($object instanceof Article) {
            $article = &$object;
            $article->setDate(new \DateTime());

            if (!is_null($file = $this->request->files->get('image'))) {
                $filename = uniqid() . '.' . $file->guessExtension();

                $image = new Image();
                $image->setSrc($filename);
                $image->setTitle($article->getTitle());
                $image->setArticle($article);
                $article->addImage($image);
                $file->move($this->container->getParameter('images_directory'), $filename);
            }
        }

        return $object;
    }
}