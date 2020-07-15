<?php

namespace App\Controller;

use App\Entity\Credit;
use App\EntityManager\UserManager;
use App\Form\CreditType;
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
    public function creditListingAction(Request $request, EntityManagerInterface $entityManager, MailHelper $mailHelper, UserManager $userManager, string $role)
    {
        $roles = $this->getUser()->getRoles();
        $credits = $entityManager->getRepository(Credit::class)->findByProducers($userManager->getProducers($this->getUser()));
        $options = [
            'role' => $role,
            'title' => 'Avoirs',
            'credits' => $credits,
        ];

        if (!in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_REFERENT', $roles)) {
            $mailsParameters = [];
            foreach ($credits as $credit) {
                $mailsParameters[$credit->getId()] = [
                    'list' => [$credit->getProducer()->getParent()],
                    'subject' => $credit->isActive() ? 'Demande de désactivation d\'avoir AMAP hommes de terre' : 'Demande d\'activation d\'avoir AMAP hommes de terre',
                    'template' => $credit->isActive() ? 'emails/deactivatecredit' : 'emails/activatecredit',
                    'mailOptions' => [
                        'credit' => $credit,
                        'role' => in_array('ROLE_ADMIN', $credit->getProducer()->getParent()->getRoles()) ? 'admin' : 'referent',
                    ],
                ];
            }
            if (!empty($mailsParameters)) {
                $options = array_merge($options, $mailHelper->createMailForm($request, $mailsParameters));
                if (isset($options['callback']) && '' != $options['callback']) {
                    return $this->redirect(urldecode($options['callback']));
                }
            }
        }

        return $this->render('credit/index.html.twig', $options);
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

            return $form->get('submitandnew')->isClicked() ?
                $this->redirectToRoute('credit_form', ['role' => $role]) :
                $this->redirectToRoute('credit_index', ['role' => $role]);
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

        return $this->forward('App\Controller\CreditController::creditListingAction', [
            'role' => $role,
        ]);
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

        return $this->forward('App\Controller\CreditController::creditListingAction', ['role' => 'admin']);
    }
}
