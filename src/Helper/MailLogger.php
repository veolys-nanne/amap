<?php

namespace App\Helper;

use App\Entity\MailLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MailLogger implements \Swift_Events_SendListener
{
    private $entityManager;

    public function __construct(string $adminEmail, EntityManagerInterface $entityManager)
    {
        $this->adminEmail = $adminEmail;
        $this->entityManager = $entityManager;
    }

    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {
    }

    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
        if ($evt->getTransport() instanceof \Swift_Transport_SpoolTransport) {
            return;
        }

        $message = $evt->getMessage();
        $mailLog = new MailLog();
        $recipients = [];
        foreach (array_keys($message->getBcc()) as $email) {
            if ($this->adminEmail !== $email) {
                $recipients[] = $this->entityManager->getRepository(User::class)->findOneByEmail($email);
            }
        }
        $recipients = array_unique(array_filter($recipients));
        $mailLog->setRecipients(array_unique($recipients))
            ->setSubject($message->getSubject())
            ->setContent($message->getBody())
            ->setSentAt(new \Datetime())
        ;
        $this->entityManager->persist($mailLog);
        $this->entityManager->flush();
    }
}
