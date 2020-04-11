<?php

namespace App\Helper;

use App\Entity\User;
use App\Form\ContactType;
use App\Form\FormatEmailType;
use App\Form\PreviewEmailsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class MailHelper
{
    protected $adminEmail;
    protected $session;
    protected $mailer;
    protected $router;
    protected $formFactory;
    protected $twigEnvironment;
    protected $entityManager;

    public function __construct(
        string $adminEmail,
        SessionInterface $session,
        \Swift_Mailer $mailer,
        UrlGeneratorInterface $router,
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        EntityManagerInterface $entityManager
    ) {
        $this->adminEmail = $adminEmail;
        $this->session = $session;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->entityManager = $entityManager;
    }

    public function createMessageFromArray(array $parameters): ?\Swift_Message
    {
        $message = $this
            ->getMailForList($parameters['subject'], [], $parameters['to'])
            ->setBody(
                $parameters['body'] ?? '',
                'text/html'
            )
            ->addPart(
                $parameters['part'] ?? '',
                'text/plain'
            );

        return $message;
    }

    public function getMailForList(string $subject, array $recipients, array $broadcastList = null): ?\Swift_Message
    {
        $message = null;
        if (null == $broadcastList) {
            $broadcastList = [$this->adminEmail];
            array_walk($recipients, function ($data) use (&$broadcastList) {
                $broadcastList = array_merge($broadcastList, [$data->getEmail()], $data->getBroadcastList());
            });
            $broadcastList = array_unique(array_filter($broadcastList));
        }
        if (!empty($broadcastList)) {
            \Swift_Preferences::getInstance()->setCharset('utf-8');
            $message = (new \Swift_Message($subject))
                ->setFrom([$this->adminEmail])
                ->setBcc($broadcastList)
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

    public function sendMessage(EntityManagerInterface $entityManager, FormInterface $form, User $user): ?string
    {
        $broadcastList = [];
        $isPreview = $form->has('email') && $form->get('email')->get('preview')->isClicked();
        foreach ($form->get('to')->getData() as $receiver) {
            switch ($receiver) {
                case ContactType::ADMIN:
                    $broadcastList = array_merge($broadcastList, $entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_ADMIN'));
                    break;
                case ContactType::ALL:
                    $broadcastList = array_merge($broadcastList, $entityManager->getRepository(User::class)->findByActive(1));
                    break;
                case ContactType::ALL_MEMBER:
                    $broadcastList = array_merge($broadcastList, $entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_MEMBER'));
                    break;
                case ContactType::ALL_PRODUCER:
                    $broadcastList = array_merge($broadcastList, $entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_PRODUCER'));
                    break;
                case ContactType::ALL_REFERENT:
                    $broadcastList = array_merge($broadcastList, $entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_REFERENT'));
                    break;
                case ContactType::MY_PRODUCERS:
                    $broadcastList = array_merge($broadcastList, $entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_PRODUCER', $user));
                    break;
                default:
                    $broadcastList = array_merge($broadcastList, [$entityManager->getRepository(User::class)->find(explode('_', $receiver)[1])]);
                    break;
            }
        }
        $message = $this->adminMailerForm($form, $broadcastList, $form->get('subject')->getData(), 'emails/email');
        if ($isPreview) {
            $formPreview = $this->formFactory->create(PreviewEmailsType::class, ['messages' => [$message]], [
                'action' => $this->router->generate('preview'),
            ]);

            return $this->twigEnvironment->render('contact/form.html.twig', [
                'messages' => [$message],
                'formPreview' => $formPreview->createView(),
                'form' => $form->createView(),
                'title' => 'Contact',
            ]);
        }
        $this->sendMessages([$message]);

        return null;
    }

    public function sendMessages(array $messages)
    {
        foreach ($messages as $message) {
            $this->mailer->send($message);
        }
        $this->session->getFlashBag()->add('info', 'Envoie de mails réalisé.');
    }

    public function createMailForm(Request $request, array $mailsParameters): array
    {
        $results = [];
        $builder = $this->formFactory->createNamedBuilder('formMail');
        $form = $builder->add('email', FormatEmailType::class, ['label' => false, 'attr' => ['class' => 'form-popin']])->getForm();
        $results['formMail'] = $form->createView();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $results = array_merge($results, $this->handleMailForm($mailsParameters, $form));
        }

        return $results;
    }

    public function handleMailForm(array $mailsParameters, Form $form): array
    {
        $results = [];
        $isEmail = $form->has('email') && $form->get('email')->get('email')->isClicked();
        $isPreview = $form->has('email') && $form->get('email')->get('preview')->isClicked();
        if ($isEmail || $isPreview) {
            $index = $form->get('email')->get('reference')->getData();
            if (is_numeric($index)) {
                $mailsParameter = $mailsParameters[$index];
                $results['messages'] = [$this->adminMailerForm($form, $mailsParameter['list'] ?? [], $mailsParameter['subject'] ?? '', $mailsParameter['template'] ?? '', $mailsParameter['mailOptions'] ?? [])];
            } else {
                foreach ($mailsParameters[$index] as $mailsParameter) {
                    $results['messages'][] = $this->adminMailerForm($form, $mailsParameter['list'] ?? [], $mailsParameter['subject'] ?? '', $mailsParameter['template'] ?? '', $mailsParameter['mailOptions'] ?? []);
                }
            }
            if ($isPreview) {
                if (isset($results['messages'])) {
                    $url = $this->router->generate('preview', ['url' => $mailsParameters[$index]['callback'] ?? '']);
                    $results['formPreview'] = $this->formFactory->create(PreviewEmailsType::class, ['messages' => $results['messages']], [
                        'action' => $url,
                    ])->createView();
                }

                return $results;
            }
        }

        if ($isEmail) {
            if (isset($results['messages'])) {
                $this->sendMessages($results['messages']);
            }
        }

        return [];
    }

    public function adminMailerForm(Form $form, array $list, string $subject, string $template, array $mailOptions = []): \Swift_Message
    {
        $message = null;
        if (count($list) > 0) {
            $mailOptions['extra'] = $form->has('email') ? $form->get('email')->get('extra')->getData() : null;
            $recipients = [];
            array_walk($list, function ($data) use (&$recipients) {
                if ($data instanceof User) {
                    $recipients[] = $data;
                } elseif (isset($data['id'])) {
                    $recipients[] = $this->entityManager->getRepository(User::class)->find($data['id']);
                }
            });
            $message = $this->getMailForList($subject, $recipients);
            $mailOptions['message'] = $message;
            if (null !== $message) {
                $message
                    ->setBody(
                        $this->twigEnvironment->render($template.'.html.twig', array_merge($mailOptions, [
                            'recipients' => $recipients,
                        ])),
                        'text/html'
                    )
                    ->addPart(
                        $this->twigEnvironment->render($template.'.txt.twig', $mailOptions),
                        'text/plain'
                    );
            }
        }

        return $message;
    }
}
