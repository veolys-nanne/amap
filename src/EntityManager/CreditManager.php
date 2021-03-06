<?php

namespace App\EntityManager;

use App\Entity\Basket;
use App\Entity\Credit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
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

    public function generateCredit(FormInterface $form)
    {
        $start = $form->has('date') && null != $form->get('date')->getData() ? $form->get('date')->getData() : $form->get('start')->getData();
        $end = $form->has('date') && null != $form->get('date')->getData() ? clone $form->get('date')->getData() : $form->get('end')->getData();
        $product = $form->has('product') ? $form->get('product')->getData() : null;
        $member = $form->has('member') ? $form->get('member')->getData() : null;
        $quantity = $form->has('quantity') ? $form->get('quantity')->getData() : nul;
        $amounts = $this->entityManager->getRepository(Basket::class)->findAmoutByIntervalAndProductAndUser($start, $end, $product, $member, $quantity);
        foreach ($amounts as $amount) {
            if ($amount['totalAmount'] > 0) {
                $object = 'Pas de livraison pour le produit "'.$product->getName().'"';
                $object .= null != $quantity ? ' (quantité: '.$quantity.')' : '';
                $object .= $start != $end ? ' sur la période du '.$start->format('d/m/Y').' au '.$end->format('d/m/Y') : ' pour le '.$start->format('d/m/Y');
                $credit = $this->createCredit();
                $credit->setProducer($product->getProducer());
                $credit->setMember($member ? $member : $this->entityManager->getRepository(User::class)->find($amount['member']));
                $credit->setTotalAmount($amount['totalAmount']);
                $credit->setCurrentAmount($amount['totalAmount']);
                $credit->setObject($object);
                $this->entityManager->persist($credit);
            }
        }
    }
}
