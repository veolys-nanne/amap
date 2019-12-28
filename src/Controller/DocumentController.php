<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    /**
     * @Route(
     *     "/admin/document/index",
     *     name="document_index",
     * )
     */
    public function documentListingAction(EntityManagerInterface $entityManager)
    {
        $documents = $entityManager->getRepository(Document::class)->findByDeleted(false);

        return $this->render('document/index.html.twig', [
            'documents' => $documents,
            'title' => 'Produits',
        ]);
    }

    /**
     * @Route(
     *     "/admin/document/form/{id}",
     *     name="document_form",
     *     requirements={"id"="\d+"},
     *     defaults={"id"=0}
     * )
     */
    public function documentEditAction(Request $request, EntityManagerInterface $entityManager, Document $document = null)
    {
        $isNew = $document ? false : true;
        $document = $document ?? new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($document);
            $entityManager->flush();
            $this->addFlash('success', $isNew ? 'Le document a été créé.' : 'Le document a été mis à jour.');

            return $this->forward('App\Controller\DocumentController::documentListingAction');
        }

        return $this->render('document/form.html.twig', [
            'form' => $form->createView(),
            'title' => $isNew ? 'Création document' : 'Mise à jour document',
        ]);
    }

    /**
     * @Route(
     *     "/admin/document/form/tinyMceImage",
     *     name="document_form_image",
     * )
     */
    public function imageAction(Request $request)
    {
        $file = $request->files->get('file');
        $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
        try {
            $file = $file->move(
                $this->getParameter('media_directory'),
                $fileName
            );
        } catch (FileException $e) {
        }

        return new JsonResponse(['location' => '/uploads/'.$file->getFilename()]);
    }

    /**
     * @Route(
     *     "/{role}/document/{name}",
     *     name="document_view",
     *     requirements={
     *      "role"="admin|referent|producer|member",
     *     }
     * )
     */
    public function documentViewAction(EntityManagerInterface $entityManager, string $role, string $name)
    {
        $document = $entityManager->getRepository(Document::class)->findByNameAndRole($name, 'ROLE_'.strtoupper($role));

        return $this->render('document/view.html.twig', [
            'document' => $document,
            'title' => $document->getName(),
        ]);
    }

    /**
     * @Route(
     *     "/admin/document/delete/{id}",
     *     name="document_delete",
     * )
     */
    public function deleteAction(EntityManagerInterface $entityManager, Document $document)
    {
        $document->setDeleted(true);
        $entityManager->flush();
        $this->addFlash('success', 'Le document a été supprimé.');

        return $this->forward('App\Controller\DocumentController::documentListingAction');
    }

    private function generateUniqueFileName(): string
    {
        return md5(uniqid());
    }
}
