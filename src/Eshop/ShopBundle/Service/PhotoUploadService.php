<?php
namespace Eshop\ShopBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\Routing\Router;

/**
 * Another Variant to upload photos to summernote
 * Class PhotoUploadService
 * @package Eshop\ShopBundle\Service
 */
class PhotoUploadService
{

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var string
     */
    private $uploadDir;

    public function __construct(EntityManager $entityManager, $uploadDir)
    {
        $this->uploadDir = $uploadDir;
        $this->em = $entityManager;
    }

    public function upload(UploadedFile $file, $entityName)
    {
        $name = $file->getClientOriginalName();

        /** @var File $path */
        $uploadedFile = $file->move($this->uploadDir . '/summernote/' . $entityName . '/', $name);

        return $uploadedFile->getRealPath();
    }
}
