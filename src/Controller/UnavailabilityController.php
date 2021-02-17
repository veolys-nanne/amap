<?php

namespace App\Controller;

use App\Doctrine\DateKey;
use App\Entity\PlanningElement;
use App\Entity\Unavailability;
use App\EntityManager\UnavailabilityManager;
use App\Form\UnavailabilityCollectionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UnavailabilityController extends AbstractController
{
    /**
     * @Route(
     *     "/admin/unavailability",
     *     name="admin_unavailability",
     * )
     */
    public function planningUnavailabilityAction(Request $request, EntityManagerInterface $entityManager)
    {
        $date = \DateTime::createFromFormat('d/m/Y', $request->query->get('date'));

        return new JsonResponse(
            array_column(
                $entityManager->getRepository(Unavailability::class)->findByDate((new DateKey())->setTimestamp($date->getTimestamp())),
            'id', null)
        );
    }

    /**
     * @Route(
     *     "/logged/unavailability",
     *     name="unavailability",
     * )
     */
    public function planningEditUnavailabilityAction(Request $request, EntityManagerInterface $entityManager, UnavailabilityManager $unavailabilityManager)
    {
        $unavailabilities = $entityManager->getRepository(Unavailability::class)->findByMember($this->getUser());
        $planningDates = array_column($entityManager->getRepository(PlanningElement::class)->findByActivePlanning(), 'date');
        $planningDates = array_map(function (string $date) {
            return \DateTime::createFromFormat('Y-m-d', $date);
        }, $planningDates);
        $unselectableDates = array_column($entityManager->getRepository(PlanningElement::class)->findByClosedPlanning(), 'date');
        $unselectableDates = array_map(function (string $date) {
            return \DateTime::createFromFormat('Y-m-d', $date);
        }, $unselectableDates);
        $oldDates = array_map(function (Unavailability $unavailability) {
            return $unavailability->getDate();
        }, $unavailabilities);
        $form = $this->createForm(UnavailabilityCollectionType::class, ['elements' => $oldDates]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newDates = [];
            foreach ($form->get('elements')->all() as $child) {
                $newDates[] = $child->getData();
            }
            $unavailabilityManager->recordDates($newDates, $oldDates);
            $entityManager->flush();

            $this->addFlash('success', 'Vos disponibilités ont été mises à jour.');
        }

        return $this->render('unavailability/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Calendrier de disponibilités',
            'planningDates' => $planningDates,
            'unselectableDates' => $unselectableDates,
        ]);
    }
}
