<?php

namespace App\EntityManager;

use App\Entity\Credit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreditManager
{
    protected $entityManager;
    protected $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function createCredit()
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $credit = new Credit();
        $credit->setDate(new \DateTime());
        $credit->setActive(false);
        $credit->setCurrentAmount(0);
        if (in_array('ROLE_PRODUCER', $user->getRoles())) {
            $credit->setProducer($user);
        }

        return $credit;
    }
}
