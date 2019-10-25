<?php

namespace App\Repository;

use App\Entity\Basket;
use App\Entity\CreditBasketAmount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CreditBasketAmountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CreditBasketAmount::class);
    }

    public function deleteByBasket(Basket $basket)
    {
        $entityManager = $this->getEntityManager();
        $sql = '
            UPDATE amap_credit, amap_credit_basket_amount, amap_basket
            SET amap_credit.current_amount = amap_credit.current_amount + amap_credit_basket_amount.amount
            WHERE amap_credit.id = amap_credit_basket_amount.credit_id AND amap_basket.id = amap_credit_basket_amount.basket_id AND (amap_basket.id = :basket OR amap_basket.parent_id = :basket);
            DELETE FROM amap_credit_basket_amount USING amap_credit_basket_amount
            INNER JOIN amap_basket ON amap_basket.id = amap_credit_basket_amount.basket_id
            WHERE amap_basket.id=:basket OR amap_basket.parent_id=:basket;
        ;';
        $statement = $entityManager->getConnection()->prepare($sql);
        $statement->bindValue('basket', $basket->getId());
        $statement->execute();
    }

    public function insert(array $creditBasketAmounts)
    {
        $entityManager = $this->getEntityManager();
        foreach ($creditBasketAmounts as $creditBasketAmount) {
            $sql = '
                INSERT INTO amap_credit_basket_amount (credit_id, basket_id, amount) VALUES (:credit_id, :basket_id, :amount);
            ;';
            $statement = $entityManager->getConnection()->prepare($sql);
            $statement->bindValue('credit_id', $creditBasketAmount['credit']);
            $statement->bindValue('basket_id', $creditBasketAmount['basket']);
            $statement->bindValue('amount', $creditBasketAmount['amount']);
            $statement->execute();
        }
    }
}
