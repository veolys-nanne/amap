<?php

namespace App\EntityManager;

use App\Entity\AvailabilitySchedule;
use App\Entity\AvailabilityScheduleElement;
use App\Entity\Planning;
use App\Helper\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PlanningManager
{
    protected $entityManager;
    protected $mailHelper;
    protected $twigEnvironment;
    protected $session;

    public function __construct(EntityManagerInterface $entityManager, MailHelper $mailHelper, Environment $twigEnvironment)
    {
        $this->entityManager = $entityManager;
        $this->mailHelper = $mailHelper;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function updatePlanning(Planning $planning, array $members, array $originalElements)
    {
        $availabilitySchedules = $this->entityManager->getRepository(AvailabilitySchedule::class)->findByPlanning($planning);
        $previousMembers = [];
        foreach ($availabilitySchedules as $availabilitySchedule) {
            $previousMembers[] = $availabilitySchedule->getMember();
        }
        $removeMembers = array_diff($previousMembers, $members);
        $addMembers = array_diff($members, $previousMembers);
        $updateMembers = array_intersect($members, $previousMembers);

        /*remove*/
        foreach ($availabilitySchedules as $availabilitySchedule) {
            if (in_array($availabilitySchedule->getMember(), $removeMembers)) {
                $this->entityManager->remove($availabilitySchedule);
            }
        }
        /*add*/
        foreach ($addMembers as $member) {
            $availabilitySchedule = new AvailabilitySchedule();
            $availabilitySchedule->setPlanning($planning);
            $availabilitySchedule->setMember($member);
            foreach ($planning->getElements() as $element) {
                $availabilityScheduleElement = new AvailabilityScheduleElement();
                $availabilityScheduleElement->setDate($element->getDate());
                $availabilityScheduleElement->setIsAvailable(false);
                $availabilityScheduleElement->setAvailabilitySchedule($availabilitySchedule);
                $availabilitySchedule->addElement($availabilityScheduleElement);
                $this->entityManager->persist($availabilityScheduleElement);
            }
            $this->entityManager->persist($availabilitySchedule);
        }
        /*update*/
        $dates = [];
        foreach ($planning->getElements() as $element) {
            $dates[] = $element->getDate()->format('Y-m-d');
        }
        foreach ($availabilitySchedules as $availabilitySchedule) {
            if (in_array($availabilitySchedule->getMember(), $updateMembers)) {
                $elements = $availabilitySchedule->getElements();
                $previousDates = [];
                foreach ($elements as $element) {
                    $previousDates[] = $element->getDate()->format('Y-m-d');
                }
                $removeDates = array_diff($previousDates, $dates);
                $addDates = array_diff($dates, $previousDates);
                foreach ($addDates as $date) {
                    $availabilityScheduleElement = new AvailabilityScheduleElement();
                    $availabilityScheduleElement->setDate(\DateTime::createFromFormat('Y-m-d', $date));
                    $availabilityScheduleElement->setIsAvailable(false);
                    $availabilityScheduleElement->setAvailabilitySchedule($availabilitySchedule);
                    $availabilitySchedule->addElement($availabilityScheduleElement);
                    $this->entityManager->persist($availabilityScheduleElement);
                }
                foreach ($availabilitySchedule->getElements() as $element) {
                    if (in_array($element->getDate()->format('Y-m-d'), $removeDates)) {
                        $availabilitySchedule->getElements()->removeElement($element);
                        $this->entityManager->remove($element);
                    }
                }
                $this->entityManager->persist($availabilitySchedule);
            }
        }
        foreach ($originalElements as $element) {
            if (false === $planning->getElements()->contains($element)) {
                $this->entityManager->remove($element);
            }
        }
        foreach ($planning->getElements() as $element) {
            $element->setPlanning($planning);
        }

        return $planning;
    }

    public function updateMembers(Planning $planning, array $originalElements)
    {
        $removeElements = array_diff($originalElements, $planning->getElements()->toArray());
        $addElements = array_diff($planning->getElements()->toArray(), $originalElements);

        foreach ($removeElements as $element) {
            $this->entityManager->remove($element);
        }

        foreach ($addElements as $element) {
            $this->entityManager->persist($element);
        }
    }

    public function changeState(Request $request, RedirectResponse $redirectResponse, Planning $planning, string $extra): Response
    {
        $messages = [];
        switch ($planning->getState()) {
            case Planning::STATE_OPEN:
                $message = $this->mailHelper->getMailForMembers('Disponibilités pour le planning des permanences AMAP Hommes de terre');
                if (null !== $message) {
                    $message
                        ->setBody(
                            $this->twigEnvironment->render('emails/availabilities.html.twig', [
                                'message' => $message,
                                'period' => $this->entityManager->getRepository(Planning::class)->findPeriodByPlanning($planning)[0],
                                'extra' => $extra,
                            ]),
                            'text/html'
                        )
                        ->addPart(
                            $this->twigEnvironment->render('emails/availabilities.txt.twig', [
                                'message' => $message,
                                'period' => $this->entityManager->getRepository(Planning::class)->findPeriodByPlanning($planning)[0],
                                'extra' => $extra,
                            ]),
                            'text/plain'
                        );
                    $messages[] = $message;
                }
                break;
            case Planning::STATE_ONLINE:
                $message = $this->mailHelper->getMailForMembers('Planning des permanences AMAP Hommes de terre');
                if (null !== $message) {
                    $message
                        ->setBody(
                            $this->twigEnvironment->render('emails/planning.html.twig', [
                                'message' => $message,
                                'period' => $this->entityManager->getRepository(Planning::class)->findPeriodByPlanning($planning)[0],
                                'extra' => $extra,
                            ]),
                            'text/html'
                        )
                        ->addPart(
                            $this->twigEnvironment->render('emails/planning.txt.twig', [
                                'message' => $message,
                                'period' => $this->entityManager->getRepository(Planning::class)->findPeriodByPlanning($planning)[0],
                                'extra' => $extra,
                            ]),
                            'text/plain'
                        );
                    $messages[] = $message;
                }
                break;
        }

        return $this->mailHelper->sendMessages($request, $messages, $redirectResponse);
    }
}
