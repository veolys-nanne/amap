<?php

namespace App\Controller;

use App\Entity\Credit;
use App\Form\CreditType;
use App\Entity\User;
use App\EntityManager\CreditManager;
use App\Helper\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CreditController extends AbstractController
{
    /**
     * @Route(
     *     "/{role}/credit/index",
     *     name="credit_index",
     *     requirements={"role"="admin|referent|producer"}
     * )
     */
    public function creditListingAction(EntityManagerInterface $entityManager, string $role)
    {
        $roles = $this->getUser()->getRoles();
        $credits = [];
        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_REFERENT', $roles)) {
            $producers = $entityManager->getRepository(User::class)->findByRole('ROLE_PRODUCER', $this->getUser());
            $credits = $entityManager->getRepository(Credit::class)->findByProducers($producers);
        } elseif (in_array('ROLE_PRODUCER', $roles)) {
            $credits = $entityManager->getRepository(Credit::class)->findByProducers([$this->getUser()]);
        }

        return $this->render('credit/index.html.twig', [
            'role' => $role,
            'credits' => $credits,
            'title' => 'Avoirs',
        ]);
    }

    /**
     * @Route(
     *     "/{role}/credit/form/{id}",
     *     name="credit_form",
     *     requirements={"role"="admin|referent|producer", "id"="\d+"},
     *     defaults={"id"=0}
     * )
     */
    public function creditEditAction(Request $request, EntityManagerInterface $entityManager, CreditManager $creditManager, string $role, Credit $credit = null)
    {
        $isNew = $credit ? false : true;
        $credit = $credit ?? $creditManager->createCredit();
        $roles = $this->getUser()->getRoles();
        $disabled = (!in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_REFERENT', $roles)) || $credit->isActive();
        $options = array_merge(['disabled' => $disabled], ['user' => $this->getUser()]);
        $form = $this->createForm(CreditType::class, $credit, $options);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($credit);
            if ($isNew) {
                $credit->setCurrentAmount($credit->getTotalAmount());
            }
            $entityManager->flush();
            $this->addFlash('success', $isNew ? 'L\'avoir a été créé.' : 'L\'avoir a été mis à jour.');

            return $this->redirectToRoute('credit_index', ['role' => $role]);
        }

        return $this->render('credit/form.html.twig', [
            'form' => $form->createView(),
            'title' => $isNew ? 'Création avoir' : 'Mise à jour avoir',
        ]);
    }

    /**
     * @Route(
     *     "/{role}/credit/active/{id}",
     *     name="credit_active",
     *     requirements={"role"="admin|referent|producer"}
     * )
     */
    public function activeAction(EntityManagerInterface $entityManager, string $role, Credit $credit)
    {
        $active = !$credit->IsActive();

        $credit->setActive($active);
        $entityManager->persist($credit);
        $entityManager->flush();
        $this->addFlash('success', $active ? 'L\'avoir a été activé.' : 'L\'avoir a été désactivé.');

        return $this->redirectToRoute('credit_index', [
            'role' => $role,
        ]);
    }

    /**
     * @Route(
     *     "/producer/credit/ask_active/{id}",
     *     name="credit_ask_active",
     * )
     */
    public function askActiveAction(Request $request, Credit $credit, MailHelper $mailHelper)
    {
        $referent = $credit->getProducer()->getParent();
        $messages = [];
        if ($credit->IsActive()) {
            $message = $mailHelper->getMailForList('Demande de désactivation d\'avoir AMAP hommes de terre', [$referent]);
            if (null !== $message) {
                $message
                    ->setBody(
                        $this->renderView('emails/deactivatecredit.html.twig', [
                            'message' => $message,
                            'credit' => $credit,
                            'role' => in_array('ROLE_ADMIN', $referent->getRoles()) ? 'admin' : 'referent',
                            'extra' => $request->request->has('extra') ? $request->request->get('extra') : '',
                        ]),
                        'text/html'
                    )
                    ->addPart(
                        $this->renderView('emails/deactivatecredit.txt.twig', [
                            'message' => $message,
                            'credit' => $credit,
                            'role' => in_array('ROLE_ADMIN', $referent->getRoles()) ? 'admin' : 'referent',
                            'extra' => $request->request->has('extra') ? $request->request->get('extra') : '',
                        ]),
                        'text/plain'
                    );
                $messages[] = $message;
            }
        } else {
            $message = $mailHelper->getMailForList('Demande d\'activation d\'avoir AMAP hommes de terre', [$referent]);
            if (null !== $message) {
                $message
                    ->setBody(
                        $this->renderView('emails/activatecredit.html.twig', [
                            'message' => $message,
                            'credit' => $credit,
                            'role' => in_array('ROLE_ADMIN', $referent->getRoles()) ? 'admin' : 'referent',
                            'extra' => $request->request->has('extra') ? $request->request->get('extra') : '',
                        ]),
                        'text/html'
                    )
                    ->addPart(
                        $this->renderView('emails/activatecredit.txt.twig', [
                            'message' => $message,
                            'credit' => $credit,
                            'role' => in_array('ROLE_ADMIN', $referent->getRoles()) ? 'admin' : 'referent',
                            'extra' => $request->request->has('extra') ? $request->request->get('extra') : '',
                        ]),
                        'text/plain'
                    );
                $messages[] = $message;
            }
        }

        return $mailHelper->sendMessages($request->request->get('preview'), $messages, $this->redirectToRoute('credit_index', [
            'role' => 'producer',
        ]));
    }

    /**
     * @Route(
     *     "/admin/credit/delete/{id}",
     *     name="credit_delete",
     * )
     */
    public function deleteAction(EntityManagerInterface $entityManager, Credit $credit)
    {
        $credit->setDeleted(true);
        $entityManager->flush();
        $this->addFlash('success', 'L\'avoir a été supprimé.');

        return $this->redirectToRoute('credit_index', ['role' => 'admin']);
    }
}
