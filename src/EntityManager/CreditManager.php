<?php

namespace App\EntityManager;

use App\Entity\Basket;
use App\Entity\Credit;
use App\Entity\Product;
use App\Entity\User;
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

    public function generateCredit(\DateTime $start, \DateTime $end, Product $product, User $member = null, int $quantity = null)
    {
        $amounts = $this->entityManager->getRepository(Basket::class)->findAmoutByIntervalAndProductAndUser($start, $end, $product, $member, $quantity);
        foreach ($amounts as $amount) {
            if ($amount['totalAmount'] > 0) {
                $credit = $this->createCredit();
                $credit->setProducer($product->getProducer());
                $credit->setMember($member ? $member : $this->entityManager->getRepository(User::class)->find($amount['member']));
                $credit->setTotalAmount($amount['totalAmount']);
                $this->entityManager->persist($credit);
            }
        }
    }
}
