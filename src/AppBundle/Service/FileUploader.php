<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Class FileUploader
 * @package AppBundle\Service
 */
class FileUploader
{
    /**
     * @var string $imagesDirectory
     */
    protected $imagesDirectory;

    /**
     * FileUploader constructor.
     * @param string $imagesDir
     */
    public function __construct($imagesDir)
    {
        $this->imagesDirectory = $imagesDir;
    }

    /**
     * @param File $file
     * @return string
     */
    public function uploadFile(File $file)
    {
        $filename = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move($this->imagesDirectory, $filename);

        return $filename;
    }

    /**
     * @param string $file
     */
    public function removeFile($file)
    {
        if(is_file($file = $this->imagesDirectory . '/' . $file)) {
            unlink($file);
        }
    }
}