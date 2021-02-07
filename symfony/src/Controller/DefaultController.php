<?php

namespace App\Controller;

use App\Entity\FileUpload;
use App\Form\FileUploadType;
use App\Service\CaptchaService;
use App\Service\FormUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @param Request           $request
     * @param CaptchaService    $captchaService
     * @param FormUploadService $formUploadService
     * @return Response
     */
    public function index(Request $request, CaptchaService $captchaService, FormUploadService $formUploadService): Response
    {
        $fileUpload = new FileUpload();
        $form       = $this->createForm(FileUploadType::class, $fileUpload);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $captchaService->validate($request) && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            if ($file && !$formUploadService->isFileExist($file) && $formUploadService->upload($file)) {
                $formUploadService->save($fileUpload, $file);
                return $this->redirectToRoute('index');
            }
        }
        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
