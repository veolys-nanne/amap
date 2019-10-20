<?php
namespace App\Controller;

use App\Entity\AvailabilitySchedule;
use App\Entity\AvailabilityScheduleElement;
use App\Entity\Planning;
use App\Form\AvailabilityScheduleElementsType;
use App\Form\PlanningType;
use App\Form\PlanningWithMemberType;
use App\EntityManager\PlanningManager;
use App\Helper\MailHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PlanningController extends AbstractController
{
    /**
     * @Route(
     *     "/admin/planning/index",
     *     name="planning_index",
     * )
     */
    public function planningListingAction(Request $request, EntityManagerInterface $entityManager)
    {
        $plannings = $entityManager->getRepository(Planning::class)->findByDeleted(false);

        return $this->render('planning/index.html.twig', [
            'plannings' => $plannings,
            'title' => 'Plannings de permancence',
            'needMail' => count($entityManager->getRepository(AvailabilitySchedule::class)->findMemberByEmptyAvailability()) > 0,
        ]);
    }

    /**
     * @Route(
     *     "/admin/planning/mailsup",
     *     name="planning_mails_up",
     * )
     */
    public function mailUpAction(Request $request, EntityManagerInterface $entityManager, MailHelper $mailHelper)
    {
        $list = $entityManager->getRepository(AvailabilitySchedule::class)->findMemberByEmptyAvailability();
        $messages = [];
        if (count($list) > 0) {
            $plannings = array_map(function(int $id) use ($entityManager) {
                return $entityManager->getRepository(Planning::class)->find($id);
            }, array_keys(array_column($list, null, 'planning')));
            foreach ($plannings as $planning) {
                $message = $mailHelper->getMailForList('Relance Disponibilité AMAP hommes de terre',
                    array_filter($list, function ($item) use ($planning) {
                        return $item['planning'] == $planning->getId();
                }));
                if (null !== $message) {
                    $message
                        ->setBody(
                            $this->renderView('emails/upplanning.html.twig', [
                                'message' => $message,
                                'extra' => $request->request->has('extra') ? $request->request->get('extra') : '',
                                'period' => $entityManager->getRepository(Planning::class)->findPeriodByPlanning($planning)[0],
                            ]),
                            'text/html'
                        )
                        ->addPart(
                            $this->renderView('emails/upplanning.txt.twig', [
                                'message' => $message,
                                'extra' => $request->request->has('extra') ? $request->request->get('extra') : '',
                                'period' => $entityManager->getRepository(Planning::class)->findPeriodByPlanning($planning)[0],
                            ]),
                            'text/plain'
                        );
                    $messages[] = $message;
                }
            }
        }

        return $mailHelper->sendMessages($request, $messages, $this->redirectToRoute('planning_index'));
    }

    /**
     * @Route(
     *     "/admin/planning/form/{id}",
     *     name="planning_form",
     *     requirements={"id"="\d+"},
     *     defaults={"id"=0}
     * )
     */
    public function planningEditAction(Request $request, EntityManagerInterface $entityManager, PlanningManager $planningManager, Planning $planning=null)
    {
        $isNew = $planning ? false : true;
        $planning = $planning??new Planning();
        $isPlanningWithMember = Planning::STATE_CLOSE == $planning->getState() || Planning::STATE_ONLINE == $planning->getState();
        $form = $this->createForm($isPlanningWithMember ? PlanningWithMemberType::class : PlanningType::class, $planning);
        $originalElements = new ArrayCollection();
        foreach ($planning->getElements() as $element) {
            $originalElements->add($element);
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('members')) {
                $members = $form->get('members')->getData();
                if ($members instanceof Collection) {
                    $members = $members->toArray();
                }
                $planningManager->updatePlanning($planning, $members, $originalElements->toArray());
            } else {
                $planningManager->updateMembers($planning, $originalElements->toArray());
            }
            $entityManager->persist($planning);
            $entityManager->flush();
            $this->addFlash('success', $isNew ? 'Le planning de permanences a été créé.' : 'Le planning de permanences a été mis à jour.');

            return $this->redirectToRoute('planning_index');
        }

        return $this->render($isPlanningWithMember ? 'planning/formwithmember.html.twig' : 'planning/form.html.twig', [
            'form' => $form->createView(),
            'title' => $isNew ? 'Création planning permanences' : 'Mise à jour planning permanences'
        ]);
    }

    /**
     * @Route(
     *     "/admin/planning/state/{state}/{id}",
     *     name="planning_state",
     * )
     */
    public function stateAction(Request $request, EntityManagerInterface $entityManager, PlanningManager $planningManager, string $state, Planning $planning)
    {
        $planning->setState($state);
        $response = $planningManager->changeState($request, $this->redirectToRoute('planning_index'), $planning, $request->request->has('extra') ? $request->request->get('extra') : '');
        if(!$request->request->get('preview')) {
            $entityManager->persist($planning);
            $entityManager->flush();
            $this->addFlash('success', 'Le planning de permanences a été passé dans l\'état "'.Planning::LABELS[$state].'".');
        }

        return $response;
    }

    /**
     * @Route(
     *     "/member/planning/availability",
     *     name="planning_availability",
     * )
     */
    public function availabilityAction(Request $request, EntityManagerInterface $entityManager)
    {
        $options = ['title' => 'Permanences',];
        $availabilityScheduleElements = $entityManager->getRepository(AvailabilityScheduleElement::class)->findByMemberAndByState($this->getUser(), Planning::STATE_OPEN);
        if (count($availabilityScheduleElements) > 0) {
            $form = $this->createForm(AvailabilityScheduleElementsType::class, $availabilityScheduleElements, ['label' => 'Mes disponibilités']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                foreach ($availabilityScheduleElements as $availabilityScheduleElement) {
                    $entityManager->persist($availabilityScheduleElement);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Vos diponibilités ont été mise à jour.');
            }
            $options['form'] = $form->createView();
        }
        $plannings = $entityManager->getRepository(Planning::class)->findByOnline();
        if(!empty($plannings)) {
            $options['plannings'] = $plannings;
        }

        return $this->render('planning/availability.html.twig', $options);
    }

    /**
     * @Route(
     *     "/admin/planning/availability/{date}/{id}",
     *     name="change_planning_availability",
     * )
     * @ParamConverter("date", options={"format": "Y-m-d"})
     */
    public function changeAvailabilityAction(Request $request, EntityManagerInterface $entityManager, \DateTime $date, Planning $planning)
    {
        $options = ['title' => 'Permanences',];
        $date->setTime(0, 0, 0);
        $availabilityScheduleElements = $entityManager->getRepository(AvailabilityScheduleElement::class)->findByPlanningAndDate($planning, $date);
        if (count($availabilityScheduleElements) > 0) {
            $form = $this->createForm(AvailabilityScheduleElementsType::class, $availabilityScheduleElements, ['type' => 'member', 'label' => $availabilityScheduleElements[0]->getDate()->format('d/m/Y')]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                foreach ($availabilityScheduleElements as $availabilityScheduleElement) {
                    $entityManager->persist($availabilityScheduleElement);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Les diponibilités ont été mise à jour.');

                return $this->redirectToRoute('planning_form', ['id' => $planning->getId()]);
            }
            $options['form'] = $form->createView();
        }
        $plannings = $entityManager->getRepository(Planning::class)->findByOnline();
        if(!empty($plannings)) {
            $options['plannings'] = $plannings;
        }

        return $this->render('planning/availability.html.twig', $options);
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

        return $this->redirectToRoute('planning_index');
    }
}
