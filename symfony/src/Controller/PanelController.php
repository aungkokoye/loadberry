<?php

namespace App\Controller;

use App\Entity\FileUpload;
use App\Form\FileUploadUpdateType;
use App\Repository\FileUploadRepository;
use App\Service\FormUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * Class PanelController
 * @package App\Controller
 */
class PanelController extends AbstractController
{
    /**
     * @param Request              $request
     * @param PaginatorInterface   $paginator
     * @param FileUploadRepository $repository
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator, FileUploadRepository $repository): Response
    {
        $search = $request->query->get('search', '');
        $page   = $request->query->get('page', 1);
        $query  = $repository->createQueryBuilder('fu');
        if (!empty($search)) {
            $query->where("LOWER(fu.fileName) LIKE LOWER(:search)")
                ->orWhere("LOWER(fu.description) LIKE LOWER(:search)")
                ->setParameter('search', '%' . $search . '%');
        }
        $query->orderBy('fu.fileName', 'ASC');

        $pagination = $paginator->paginate(
            $query->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('panel/index.html.twig', [
            'pagination' => $pagination,
            'search'     => $search,
            'page'       => $page
        ]);
    }

    /**
     * @param int                  $id
     * @param FileUploadRepository $repository
     * @return BinaryFileResponse
     * @throws \Exception
     */
    public function download(int $id, FileUploadRepository $repository): BinaryFileResponse
    {
        $target     = $this->getParameter('file_upload_dir') . '/';
        $fileUpload = $this->getFileUploadByID($id, $repository);
        $path       = $target . $fileUpload->getFileName();

        $response        = new BinaryFileResponse($path);
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
        if ($mimeTypeGuesser->isSupported()) {
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guess($path));
        } else {
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'text/plain');
        }
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileUpload->getFileName()
        );

        return $response;
    }

    /**
     * @param int                    $id
     * @param FileUploadRepository   $repository
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws \Exception
     */
    public function update(int $id, FileUploadRepository $repository, Request $request, EntityManagerInterface $em): Response
    {
        $fileUpload = $this->getFileUploadByID($id, $repository);
        $form       = $this->createForm(FileUploadUpdateType::class, $fileUpload);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($fileUpload);
            $em->flush();
            $this->addFlash('success', 'Successfully updated!');
        }
        return $this->render('panel/update.html.twig', [
            'form'   => $form->createView(),
            'search' => $request->query->get('search', ''),
            'page'   => $request->query->get('page', 1)
        ]);
    }

    /**
     * @param int                    $id
     * @param EntityManagerInterface $em
     * @param FileUploadRepository   $repository
     * @param FormUploadService      $service
     * @return JsonResponse
     */
    public function ajaxDelete(
        int $id,
        EntityManagerInterface $em,
        FileUploadRepository $repository,
        FormUploadService $service
    ): JsonResponse
    {
        $result = 'success';
        try {
            $fileUpload = $this->getFileUploadByID($id, $repository);
            if($service->delete($fileUpload)) {
                $em->remove($fileUpload);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Successfully deleted!');
            } else {
                throw new \Exception('Can\'t delete file!');
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Cannot delete!');
            $result = $e->getMessage();
        }
        return new JsonResponse(['result' => $result]);
    }

    /**
     * @param int                  $id
     * @param FileUploadRepository $repository
     * @return FileUpload
     *
     * @throws \Exception
     */
    private function getFileUploadByID(int $id, FileUploadRepository $repository): FileUpload
    {
        $fileUpload = $repository->find($id);
        if (!$fileUpload instanceof FileUpload) {
            throw $this->createNotFoundException('Cannot find the request!');
        }
        return $fileUpload;
    }
}
