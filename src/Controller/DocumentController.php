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

            return $form->get('submitandnew')->isClicked() ?
                $this->redirectToRoute('document_form') :
                $this->redirectToRoute('document_index');
        }

        return $this->render('document/form.html.twig', [
            'form' => $form->createView(),
            'title' => $isNew ? 'Création document' : 'Mise à jour document',
        ]);
    }

    /**
     * @Route(
     *     "/logged/document/form/tinyMceImage",
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
     *     "/logged/document/{name}",
     *     name="document_view",
     * )
     */
    public function documentViewAction(EntityManagerInterface $entityManager, string $name)
    {
        $roles = $this->getUser()->getRoles();
        $role = 'ROLE_MEMBER';
        if (in_array('ROLE_ADMIN', $roles)) {
            $role = 'ROLE_ADMIN';
        } elseif (in_array('ROLE_REFERENT', $roles)) {
            $role = 'ROLE_REFERENT';
        } elseif (in_array('ROLE_PRODUCER', $roles)) {
            $role = 'ROLE_PRODUCER';
        }
        $documents = $entityManager->getRepository(Document::class)->findByNameAndRole($name, $role);

        return $this->render('document/view.html.twig', [
            'documents' => $documents,
            'title' => $name,
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
