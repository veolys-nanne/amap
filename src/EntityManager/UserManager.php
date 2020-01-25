<?php

namespace App\EntityManager;

use App\Entity\Credit;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    protected $entityManager;
    protected $productManager;

    public function __construct(EntityManagerInterface $entityManager, ProductManager $productManager)
    {
        $this->entityManager = $entityManager;
        $this->productManager = $productManager;
    }

    public function changeUserActivity(User $user)
    {
        if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_PRODUCER', $user->getRoles())) {
            if (in_array('ROLE_MEMBER', $user->getRoles()) && !$user->isActive()) {
                $user->removeRole('ROLE_MEMBER');
            }
            if (!in_array('ROLE_MEMBER', $user->getRoles()) && $user->isActive()) {
                $user->addRole('ROLE_MEMBER');
            }
        }
        if (in_array('ROLE_PRODUCER', $user->getRoles())) {
            $products = $this->entityManager->getRepository(Product::class)->findByProducers([$user]);
            foreach ($products as $product) {
                $product->setActive($user->isActive());
                $product->setDeleted($user->isDeleted());
                $this->entityManager->persist($product);
                $this->productManager->changeProductActivity($product);
            }
        }
    }

    public function setDeleted(User $user)
    {
        $user->setDeleted(true);
        $user->setActive(false);
        $this->changeUserActivity($user);
        if (in_array('ROLE_PRODUCER', $user->getRoles())) {
            $credits = $this->entityManager->getRepository(Credit::class)->findByProducer($user);
            foreach ($credits as $credit) {
                $credit->setDeleted(true);
                $this->entityManager->persist($credit);
            }
        }
    }

    public function getProducers(User $user)
    {
        $roles = $user->getRoles();
        $producers = [];
        if (in_array('ROLE_ADMIN', $roles)) {
            $producers = $this->entityManager->getRepository(User::class)->findByRole('ROLE_PRODUCER');
        }
        if (in_array('ROLE_REFERENT', $roles)) {
            $producers = array_merge($producers, $user->getChildren()->toArray());
        }
        if (in_array('ROLE_PRODUCER', $roles)) {
            $producers = array_merge($producers, [$user]);
        }

        return array_unique($producers);
    }
}
