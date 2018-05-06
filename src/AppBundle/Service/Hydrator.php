<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Image;
use Doctrine\ORM\Tools\Export\ExportException;

class Hydrator extends MetaService
{
    /**
     * @var array
     */
    private $methods = [];

    /**
     * Function to check whether the form is conform or not
     * Added field in comparison to the specified entity will result in
     * returning false as well as a wrong csrf_token
     *
     * @param array $classes
     * @return bool
     */
    public function isFormValid(array $classes = [])
    {
        foreach ($this->getRequest()->request as $field => $value) {

            if ($field === 'csrf_token') {
                if (!($value === $this->getSession()->get('csrf_token'))) {
                    return false;
                }
            } else {

                /* Putting all the methods of the classes passed
                   in arguments into an array
                */
                foreach ($classes as $class) {
                    $methods = get_class_methods($class);
                    array_walk($methods, [$this, 'storeMethod']);
                }

                /* If not in array => the form has been modified before sending it */
                if (!in_array($method = 'get' . ucfirst($field), $this->methods)) {
                    dump($method);
                    dump($this->methods);
                    return false;
                }
            }
        }

        return true;
    }

    private function storeMethod($field)
    {
        $this->methods[$field] = $field;
    }

    /**
     * Function to hydrate the specified object
     * Not need to validate fields as already done
     *
     * @param $class
     * @return object
     */
    public function hydrateObject($class)
    {
        $object = new $class;

        foreach ($this->getRequest()->request as $field => $value) {
            if ($field !== "csrf_token" && method_exists($object, $method = 'set' . ucfirst($field))) {
                $object->$method(htmlspecialchars($value));
            }
        }

        if ($object instanceof Article) {
            $article = &$object;
            $article->setDate(new \DateTime());

            if (!empty($file = $this->getRequest()->files->get('image'))) {

                $filename = uniqid() . '.' . $file->guessExtension();

                $image = new Image();
                $image->setSrc($filename);
                $image->setTitle($article->getTitle());

                if (!empty($this->getRequest()->request->get('content'))) {
                    $image->setContent($this->getRequest()->request->get('content'));
                }

                $image->setArticle($article);
                $article->addImage($image);
                $file->move($this->container->getParameter('images_directory'), $filename);
            }
        }

        return $object;
    }
}