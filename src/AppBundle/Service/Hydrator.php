<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Image;

class Hydrator
{
    /**
     * @var MetaService $metaService
     */
    protected $metaService;

    /**
     * @var AppTools $appTools
     */
    protected $appTools;

    /**
     * Hydrator constructor.
     * @param MetaService $metaService
     */
    public function __construct(MetaService $metaService, AppTools $appTools)
    {
        $this->metaService = $metaService;
        $this->appTools = $appTools;
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
    public function isFormValid(array $classes = [], $sender)
    {
        foreach ($this->metaService->getRequest()->request as $field => $value) {

            if ($field === 'csrf_token') {
                if (!($value === $this->metaService->getSession()->get($sender))) {
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
                if ($field !== 'sender' && !in_array($method = 'get' . ucfirst($field), $this->methods)) {
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
     * @return Article|Comment
     * @param $class
     */
    public function hydrateObject($class)
    {
        /* All entities */
        $object = $this->hydrator(new $class);

        if ($object instanceof Article) {
            $object->setDate(new \DateTime());
            $this->imageCreator($object);
            $this->categoryCreator($object);
            $this->addPDF($object);
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
            $this->categoryCreator($object);
            $this->addPDF($object);
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
                if ($object instanceof Article && $field === 'title'
                    && ($this->metaService->getRequest()->get('title') !== $object->getTitle()
                        || is_null($object->getSlug()))
                    ) {
                    $slug = $this->appTools->slugify($value);
                    $object->setSlug($slug);
                }

                if ($object instanceof Article && $field === "newsletter") {
                    $object->setNewsletter($value === "on");
                } else {
                    $object->$method(htmlspecialchars($value));
                }
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
                    if (!empty($images[$key])) {
                        $this->metaService->getEntityManager()->remove($images[$key]);
                    }
                    $filename = $this->metaService->getFileUploader()->uploadFile($file);
                    $image->setTitle($article->getSlug());
                    $image->setArticle($article);
                    $image->setSrc($filename);
                    $article->addImage($image);
                    $image->setContent($content);
                } else {
                    $images[$key]->setTitle($article->getSlug());
                    $images[$key]->setContent($content);
                }
            }
        }
    }

    private function categoryCreator(Article $article)
    {
        $categories = $this->metaService->getRequest()->get('category');
        $articleCategories = $article->getCategories();
        $cat = null;

        foreach ($articleCategories as $key => $category) {
            if (empty($categories) || !array_key_exists($key, $categories)) {
                $article->removeCategory($category);
            }
        }

        if (!empty($categories)) {

            foreach ($categories as $category) {

                if (is_null($entity = $this->metaService->getEntityManager()->getRepository(Category::class)->findOneBy(['category' => $category]))) {
                    $cat = (new Category())->setCategory(strtolower($category));
                }

                if (!is_null($entity)) {
                    $entity->setCategory(strtolower($category));
                }

                if (!$articleCategories->contains($cat = !is_null($entity) ? $entity : $cat)) {
                    $article->addCategory($cat);
                    $cat->addArticle($article);
                }
            }
        }
    }

    private function addPDF(Article $article)
    {
        if (!is_null($pdf = $this->metaService->getRequest()->files->get("pdf"))) {

            if (!is_null($article->getPdf())) {
                $this->metaService->getFileUploader()->removeFile($article->getPdf());
            }

            $filename = $this->metaService->getFileUploader()->uploadFile($pdf);
            $article->setPdf($filename);
        }
    }
}