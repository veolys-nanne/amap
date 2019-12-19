<?php

namespace App\Helper;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MailHelper
{
    protected $entityManager;
    protected $adminEmail = '';
    protected $session;
    protected $mailer;
    protected $router;

    public function __construct(EntityManagerInterface $entityManager, string $adminEmail, SessionInterface $session, \Swift_Mailer $mailer, UrlGeneratorInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->adminEmail = $adminEmail;
        $this->session = $session;
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function getMailForMembers(string $subject): ?\Swift_Message
    {
        return $this->getMailForList($subject, $this->entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_MEMBER'));
    }

    public function getMailForList(string $subject, array $list): ?\Swift_Message
    {
        $message = null;
        $broadcastList = [$this->adminEmail];
        array_walk($list, function ($data) use (&$broadcastList) {
            $broadcastList = $data instanceof User ? array_merge($broadcastList, [$data->getEmail()], $data->getBroadcastList()) : array_merge($broadcastList, [$data['email']], $data['broadcastList']);
        });
        $broadcastList = array_unique(array_filter($broadcastList));
        if (!empty($broadcastList)) {
            \Swift_Preferences::getInstance()->setCharset('utf-8');
            $message = (new \Swift_Message($subject))
                ->setFrom([$this->adminEmail])
                ->setTo($broadcastList)
            ;
            $headers = $message->getHeaders();
            if (isset($_SERVER['SERVER_NAME'])) {
                $headers->addIdHeader('Message-ID', time().'@'.$_SERVER['SERVER_NAME']);
            }
            $headers->addTextHeader('X-Mailer', 'PHP v'.phpversion());
            $headers->addTextHeader('List-Unsubscribe', $this->router->generate('unsubscribe'));
        }

        return $message;
    }

    public function sendMessages(bool $isPreview, array $messages, RedirectResponse $redirectResponse): Response
    {
        if ($isPreview) {
            $jsonResponse = [];
            foreach ($messages as $message) {
                $jsonResponse[] = [
                    'from' => $message->getFrom(),
                    'to' => $message->getTo(),
                    'subject' => $message->getSubject(),
                    'body' => $message->getBody(),
                ];
            }

            return new JsonResponse($jsonResponse);
        }

        foreach ($messages as $message) {
            $this->mailer->send($message);
        }
        $this->session->getFlashBag()->add('info', 'Envoie de mails réalisé.');

        return $redirectResponse;
    }
}
