<?php

namespace App\Controller;

use App\Entity\MailLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function maillogViewAction(MailLog $maillog)
    {
        return $this->render('maillog/view.html.twig', [
            'maillog' => $maillog,
            'title' => 'Consultation de mail',
        ]);
    }

    /**
     * @Route(
     *     "/{role}/maillog/delete/{id}",
     *     name="maillog_delete",
     *     requirements={"id"="\d+", "role"="admin|referent|producer|member"},
     * )
     */
    public function deleteAction(EntityManagerInterface $entityManager, MailLog $maillog)
    {
        $maillog->setDeleted(true);
        $entityManager->flush();
        $this->addFlash('success', 'Le mail a été supprimé.');

        return $this->forward('App\Controller\MailLogController::maillogListingAction');
    }
}
