<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Image;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Hydrator
{
    /**
     * @var MetaService $metaService
     */
    protected $metaService;

    /**
     * Hydrator constructor.
     * @param MetaService $metaService
     */
    public function __construct(MetaService $metaService)
    {
        $this->metaService = $metaService;
    }

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
        foreach ($this->metaService->getRequest()->request as $field => $value) {

            if ($field === 'csrf_token') {
                if (!($value === $this->metaService->getSession()->get('csrf_token'))) {
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
     * @return object
     * @param $class
     */
    public function hydrateObject($class)
    {
        /* All entities */
        $object = $this->hydrator(new $class);

        if ($object instanceof Article) {
            $object->setDate(new \DateTime());
            $this->imageCreator($object);
        }

        return $object;
    }

    /**
     * @param object $object
     */
    public function updateObject($object)
    {
        $object = $this->hydrator($object);

        if ($object instanceof Article) {
            $this->imageCreator($object);
        }

        $this->metaService->flush();
    }

    /**
     * @param object $object
     * @return object
     */
    private function hydrator($object)
    {
        foreach ($this->metaService->getRequest()->request as $field => $value) {
            if ($field !== "csrf_token" && method_exists($object, $method = 'set' . ucfirst($field))) {
                if ($object instanceof Article && $field === 'title') {
                    $slug = AppTools::slugify($value);
                    $object->setSlug($slug);
                }

                $object->$method(htmlspecialchars($value));
            }
        }

        return $object;
    }

    /**
     * @param Article $object
     * @return object
     */
    private function imageCreator(Article $object)
    {
        if (!is_dir($folder = $this->metaService->getParameter('images_directory'))) {
            mkdir($folder);
        }

        $contents = $this->metaService->getRequest()->get('content');
        $files = $this->metaService->getRequest()->files->get('image');

        $article = &$object;
        $images = $article->getImages();

        /* If removing all the forms then adding the same number of forms */
        /* As the key is not decremented when removing the form */
        /* The key corresponding to the new form will not match any key from article images collection */
        /* Thus, all the article images will be removed */
        foreach ($images as $key => $image) {
            if (empty($contents) || !array_key_exists($key, $contents)) {
                $this->metaService->getEntityManager()->remove($image);
            }
        }


        if (!empty($contents)) {

            foreach ($contents as $key => $content) {

                if (array_key_exists($key, $files) && !is_null($file = $files[$key])) {
                    $image = new Image();
                    if(!empty($images[$key])) {
                        $this->metaService->getEntityManager()->remove($images[$key]);
                    }
                    $filename = $this->metaService->getFileUploader()->uploadFile($file);
                    $image->setSrc($filename);
                    $image->setTitle($article->getSlug());
                    $image->setArticle($article);
                    $article->addImage($image);
                    $image->setContent($contents[$key]);
                }
            }
        }

        return $object;
    }
}