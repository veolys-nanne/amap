<?php

namespace App\EntityManager;

use App\Entity\Unavailability;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UnavailabilityManager
{
    protected $entityManager;
    protected $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function recordDates($newDates, $oldDates)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $addDates = array_udiff($newDates, $oldDates, function (\DateTime $dateA, \DateTime $dateB) {
            return $dateA->format('Y-m-d') != $dateB->format('Y-m-d');
        });
        foreach ($addDates as $addDate) {
            $unavailability = new Unavailability();
            $unavailability->setDate($addDate);
            $unavailability->setMember($user);
            $this->entityManager->persist($unavailability);
        }
        $this->entityManager->getRepository(Unavailability::class)->remove($user,
            array_udiff($oldDates, $newDates, function (\DateTime $dateA, \DateTime $dateB) {
                return $dateA->format('Y-m-d') != $dateB->format('Y-m-d');
            })
        );
    }
}
