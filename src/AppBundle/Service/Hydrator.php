<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Image;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * No need to validate fields as already done
     * @return JsonResponse
     * @param $class
     */
    public function hydrateObject($class)
    {
        $object = new $class;

        foreach ($this->getRequest()->request as $field => $value) {
            if ($field !== "csrf_token" && method_exists($object, $method = 'set' . ucfirst($field))) {
                if ($object instanceof Article && $field === 'title') {
                    $slug = AppTools::slugify($value);
                    $object->setSlug($slug);
                }

                $object->$method(htmlspecialchars($value));
            }
        }

        if ($object instanceof Article) {

            if (!is_dir($folder = $this->getParameter('images_directory'))) {
                mkdir($folder);
            }

            $article = &$object;
            $article->setDate(new \DateTime());

            if (!empty($files = $this->getRequest()->files->get('image'))) {

                foreach ($files as $key => $file) {
                    $filename = uniqid() . '.' . $file->guessExtension();

                    $image = new Image();
                    $image->setSrc($filename);
                    $image->setTitle($article->getSlug());

                    if (!empty($contents = $this->getRequest()->request->get('content'))) {
                        $image->setContent($contents[$key]);
                    }

                    $image->setArticle($article);
                    $article->addImage($image);
                    $file->move($folder, $filename);
                }
            }
        }

        $this->persistAndFlush([$object]);

        return new JsonResponse("created");
    }
}