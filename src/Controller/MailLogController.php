<?php

namespace App\Controller;

use App\Entity\MailLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MailLogController extends AbstractController
{
    /**
     * @Route(
     *     "/logged/admin/index",
     *     name="maillog_index",
     * )
     */
    public function maillogListingAction(EntityManagerInterface $entityManager)
    {
        $mailLogs = $entityManager->getRepository(MailLog::class)->findByRecipients($this->getUser());

        return $this->render('maillog/index.html.twig', [
            'mailLogs' => $mailLogs,
            'title' => 'Mails',
        ]);
    }

    /**
     * @Route(
     *     "/logged/maillog/view/{id}",
     *     name="maillog_view",
     *     requirements={"id"="\d+"},
     * )
     */
    public function maillogViewAction(MailLog $maillog)
    {
        return $this->render('maillog/view.html.twig', [
            'maillog' => $maillog,
            'title' => 'Consultation de mail',
        ]);
    }

    /**
     * @Route(
     *     "/logged/maillog/delete/{id}",
     *     name="maillog_delete",
     *     requirements={"id"="\d+"},
     * )
     */
    public function deleteAction(EntityManagerInterface $entityManager, MailLog $maillog)
    {
        $maillog->setDeleted(true);
        $entityManager->flush();
        $this->addFlash('success', 'Le mail a été supprimé.');

        return $this->forward('App\Controller\MailLogController::maillogListingAction');
    }

    /**
     * @Route(
     *     "/logged/maillog/update",
     *     name="maillog_update",
     * )
     */
    public function updateAction(Request $request, EntityManagerInterface $entityManager)
    {
        $mailLog = $entityManager->getRepository(MailLog::class)->find($request->request->get('id'));
        $mailLog->setContent($request->request->get('content'));
        $entityManager->flush();

        return new JsonResponse();
    }
}
