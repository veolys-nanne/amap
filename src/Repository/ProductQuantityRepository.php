<?php

namespace App\Repository;

use App\Entity\Basket;
use App\Entity\ProductQuantity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductQuantityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProductQuantity::class);
    }

    public function updateByBasket(Basket $basket)
    {
        $entityManager = $this->getEntityManager();
        if ($basket->isFrozen()) {
            $sql = '
                UPDATE amap_product_quantity, amap_basket
                SET amap_product_quantity.price = (SELECT amap_product.price FROM amap_product WHERE amap_product.id = amap_product_quantity.product_id)
                WHERE amap_basket.id = amap_product_quantity.basket_id AND (amap_basket.id = :basket OR amap_basket.parent_id = :basket);
            ;';
        } else {
            $sql = '
                UPDATE amap_product_quantity, amap_basket
                SET amap_product_quantity.price = NULL
                WHERE amap_basket.id = amap_product_quantity.basket_id AND (amap_basket.id = :basket OR amap_basket.parent_id = :basket);
                DELETE FROM amap_product_quantity USING amap_product_quantity
                INNER JOIN amap_product ON amap_product.id = amap_product_quantity.product_id
                INNER JOIN amap_basket ON amap_basket.id = amap_product_quantity.basket_id
                WHERE amap_product.active = 0 AND (amap_basket.id=:basket OR amap_basket.parent_id=:basket);
            ;';
        }
        $statement = $entityManager->getConnection()->prepare($sql);
        $statement->bindValue('basket', $basket->getId());
        $statement->execute();
    }
}
