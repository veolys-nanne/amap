<?php

namespace App\Command;

use App\Entity\PlanningElement;
use App\Helper\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlanningEmailCommand extends Command
{
    protected static $defaultName = 'app:planning-email';
    protected $mailHelper;
    protected $entityManager;
    protected $twigEnvironment;

    public function __construct(EntityManagerInterface $entityManager, MailHelper $mailHelper, \Swift_Mailer $mailer, \Twig_Environment $twigEnvironment)
    {
        $this->entityManager = $entityManager;
        $this->mailHelper = $mailHelper;
        $this->mailer = $mailer;
        $this->twigEnvironment = $twigEnvironment;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Send planning email.')
            ->setHelp('This command send mails to users who suscribed to planning at 3 and 0 days.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sendMail($this->entityManager->getRepository(PlanningElement::class)->findMembersByDate((new \DateTime())->add(new \DateInterval('P5D'))));
        $this->sendMail($this->entityManager->getRepository(PlanningElement::class)->findMembersByDate(new \DateTime()));
    }

    protected function sendMail($list)
    {
        if (0 < count($list)) {
            $message = $this->mailHelper->getMailForList('Permanence AMAP hommes de terre', $list);
            if (null !== $message) {
                $message
                    ->setBody(
                        $this->twigEnvironment->render('emails/permanence.html.twig', [
                            'message' => $message,
                            'date' => $list[0]['date'],
                        ]),
                        'text/html'
                    )
                    ->addPart(
                        $this->twigEnvironment->render('emails/permanence.txt.twig', [
                            'message' => $message,
                            'date' => $list[0]['date'],
                        ]),
                        'text/plain'
                    );
                $this->mailer->send($message);
            }
        }
    }
}
