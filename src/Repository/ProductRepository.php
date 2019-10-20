<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByProducers(array $producers): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.producer', 'producer')
            ->where('p.producer IN (:producers)')
            ->andWhere('p.deleted = 0')
            ->setParameter('producers', $producers)
            ->orderBy('producer.order', 'asc')
            ->addOrderBy('producer.lastname', 'asc')
            ->addOrderBy('p.order', 'asc')
            ->addOrderBy('p.name', 'asc')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.producer', 'producer')
            ->where('p.active = 1')
            ->andWhere('producer.active = 1')
            ->orderBy('producer.order', 'asc')
            ->addOrderBy('producer.lastname', 'asc')
            ->addOrderBy('p.order', 'asc')
            ->addOrderBy('p.name', 'asc')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneByOrders(int $producerOrder, int $productOrder): Product
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.producer', 'producer')
            ->where("producer.order = :producerOrder")
            ->andWhere("p.order = :productOrder")
            ->setParameters([
                'producerOrder' => $producerOrder,
                'productOrder' => $productOrder
            ])
            ->getQuery()
            ->getSingleResult()
            ;
    }

    public function findMaxOrder(User $producer): ?int
    {
        return $this->createQueryBuilder('p')
            ->select('MAX(p.order) as max')
            ->where("p.producer = :producer")
            ->setParameter('producer', $producer)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
}
