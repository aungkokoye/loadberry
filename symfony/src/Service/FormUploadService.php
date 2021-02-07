<?php

namespace App\Service;

use App\Entity\FileUpload;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FormUploadService
{

    /**
     * @var ContainerInterface
     */
    private $container;

    private $entityManager;

    public function __construct(ContainerInterface $container, EntityManagerInterface  $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function upload(UploadedFile $file): bool
    {
        $dir      = $this->container->getParameter('file_upload_dir');
        $fileName = $this->getFileName($file);
        try {
            $file->move($dir, $fileName);
        } catch (FileException $e) {
            $this->container->get('session')->getFlashBag()->add('error', 'Cannot upload file!');
            return false;
        }
        return true;
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function isFileExist(UploadedFile $file): bool
    {
        $filePath = $this->container->getParameter('file_upload_dir') . '/' . $this->getFileName($file);
        if ($result = file_exists($filePath)) {
            $this->container->get('session')->getFlashBag()->add('error', 'File Name already used!');
        }
        return $result;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function getFileName(UploadedFile $file): string
    {
        return pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' .
            $file->getClientOriginalExtension();;
    }

    /**
     * @param FileUpload   $fileUpload
     * @param UploadedFile $file
     */
    public function save(FileUpload $fileUpload, UploadedFile $file)  : void
    {
        $fileUpload->setFileName($this->getFileName($file));
        $fileUpload->setSize($file->getSize());
        $this->entityManager->persist($fileUpload);
        $this->entityManager->flush();
        $this->container->get('session')->getFlashBag()->add('success', 'Successfully uploaded the file!');
    }

    /**
     * @param FileUpload $fileUpload
     * @return bool
     */
    public function delete(FileUpload $fileUpload) : bool
    {
        $filePath = $this->container->getParameter('file_upload_dir') . '/' . $fileUpload->getFileName();
        if(file_exists($filePath)) {
           return unlink($filePath);
        }
        return true;
    }
}