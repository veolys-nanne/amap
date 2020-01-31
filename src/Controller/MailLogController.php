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
     *     "/{role}/admin/index",
     *     name="maillog_index",
     *     requirements={"role"="admin|referent|producer|member"},
     * )
     */
    public function maillogListingAction(EntityManagerInterface $entityManager, string $role)
    {
        $mailLogs = $entityManager->getRepository(MailLog::class)->findByRecipients($this->getUser());

        return $this->render('maillog/index.html.twig', [
            'role' => $role,
            'mailLogs' => $mailLogs,
            'title' => 'Mails',
        ]);
    }

    /**
     * @Route(
     *     "/{role}/maillog/view/{id}",
     *     name="maillog_view",
     *     requirements={"id"="\d+", "role"="admin|referent|producer|member"},
     * )
     */
    public function maillogViewAction(MailLog $maillog, string $role)
    {
        return $this->render('maillog/view.html.twig', [
            'maillog' => $maillog,
            'title' => 'Consultation de mail',
            'role' => $role,
        ]);
    }

    /**
     * @Route(
     *     "/{role}/maillog/delete/{id}",
     *     name="maillog_delete",
     *     requirements={"id"="\d+", "role"="admin|referent|producer|member"},
     * )
     */
    public function deleteAction(EntityManagerInterface $entityManager, MailLog $maillog, string $role)
    {
        $maillog->setDeleted(true);
        $entityManager->flush();
        $this->addFlash('success', 'Le mail a été supprimé.');

        return $this->forward('App\Controller\MailLogController::maillogListingAction', ['role' => $role]);
    }

    /**
     * @Route(
     *     "/{role}/maillog/update",
     *     name="maillog_update",
     *     requirements={"role"="admin|referent|producer|member"},
     * )
     */
    public function updateAction(Request $request, EntityManagerInterface $entityManager, string $role)
    {
        $mailLog = $entityManager->getRepository(MailLog::class)->find($request->request->get('id'));
        $mailLog->setContent($request->request->get('content'));
        $entityManager->flush();

        return new JsonResponse();
    }
}
