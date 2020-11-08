<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Entity\PlanningElement;
use App\Entity\Unavailability;
use App\Entity\User;
use App\EntityManager\PlanningManager;
use App\Form\PlanningType;
use App\Helper\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    /**
     * @Route(
     *     "/admin/planning/index",
     *     name="planning_index",
     * )
     */
    public function planningListingAction(Request $request, EntityManagerInterface $entityManager, PlanningManager $planningManager, MailHelper $mailHelper)
    {
        $plannings = $entityManager->getRepository(Planning::class)->findByDeleted(false);
        $options = [
            'plannings' => $plannings,
            'title' => 'Plannings de permancence',
        ];

        $mailsParameters = [];
        foreach ($plannings as $planning) {
            if ($parameters = $planningManager->getNextStateMail($planning)) {
                $mailsParameters['nextStateMail_'.$planning->getId()] = $parameters;
            }
            if ($parameters = $planningManager->getUpMail($planning)) {
                $mailsParameters['upMail_'.$planning->getId()] = $parameters;
            }
        }

        if (!empty($mailsParameters)) {
            $options = array_merge($options, $mailHelper->createMailForm($request, $mailsParameters), ['mailsParameters' => $mailsParameters]);
            if (isset($options['callback']) && '' != $options['callback']) {
                return $this->redirect(urldecode($options['callback']));
            }
        }

        return $this->render('planning/index.html.twig', $options);
    }

    /**
     * @Route(
     *     "/admin/planning/form/{id}",
     *     name="planning_form",
     *     requirements={"id"="\d+"},
     *     defaults={"id"=0}
     * )
     */
    public function planningEditAction(Request $request, EntityManagerInterface $entityManager, Planning $planning = null)
    {
        $isNew = $planning ? false : true;
        $planning = $isNew ? new Planning() : $planning;
        $originalElements = $planning->getElements()->toArray();
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($planning->getElements() as $element) {
                $element->setPlanning($planning);
            }
            foreach ($originalElements as $element) {
                if (false === $planning->getElements()->contains($element)) {
                    $entityManager->remove($element);
                }
            }
            $entityManager->persist($planning);
            $entityManager->flush();
            $this->addFlash('success', $isNew ? 'Le planning de permanences a été créé.' : 'Le planning de permanences a été mis à jour.');

            if (Planning::STATE_CLOSE !== $planning->getState()) {
                return $this->forward('App\Controller\PlanningController::planningListingAction');
            }
        }

        $planningDates = array_column($entityManager->getRepository(PlanningElement::class)->findByActivePlanning($planning), 'date');
        $planningDates = array_map(function (string $date) {
            return \DateTime::createFromFormat('Y-m-d', $date);
        }, $planningDates);

        return $this->render('planning/form.html.twig', [
            'form' => $form->createView(),
            'title' => $isNew ? 'Création planning permanences' : 'Mise à jour planning permanences',
            'planningDates' => $planningDates,
        ]);
    }

    /**
     * @Route(
     *     "/admin/planning/delete/{id}",
     *     name="planning_delete",
     * )
     */
    public function deleteAction(EntityManagerInterface $entityManager, Planning $planning)
    {
        $planning->setDeleted(true);
        $entityManager->flush();
        $this->addFlash('success', 'Le planning a été supprimé.');

        return $this->forward('App\Controller\PlanningController::planningListingAction');
    }

    /**
     * @Route(
     *     "/admin/planning/state/{state}/{id}",
     *     name="planning_state",
     * )
     */
    public function stateAction(Request $request, EntityManagerInterface $entityManager, string $state, Planning $planning)
    {
        $planning->setState($state);
        if (!$request->request->get('preview')) {
            $entityManager->persist($planning);
            $entityManager->flush();
            if (Planning::STATE_OPEN == $state) {
                $users = $entityManager->getRepository(User::class)->findMemberActiveWithOtherRole();
                foreach ($users as $user) {
                    foreach ($planning->getElements() as $element) {
                        $unavailability = $entityManager->getRepository(Unavailability::class)->findByUserAndDate($user, $element->getDate()) ?? new Unavailability();
                        $unavailability->setDate($element->getDate());
                        $unavailability->setMember($user);
                        $entityManager->persist($unavailability);
                    }
                }
                $entityManager->flush();
            }
            $this->addFlash('success', 'Le planning de permanences a été passé dans l\'état "'.PlanningManager::LABELS[$state].'".');
        }

        return $this->forward('App\Controller\PlanningController::planningListingAction');
    }

    /**
     * @Route(
     *     "/logged/planning",
     *     name="planning",
     * )
     */
    public function planningViewAction(EntityManagerInterface $entityManager)
    {
        return $this->render('planning/view.html.twig', [
            'planningElements' => $entityManager->getRepository(PlanningElement::class)->findByOnline(),
        ]);
    }
}
