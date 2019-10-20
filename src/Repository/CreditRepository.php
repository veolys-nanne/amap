<?php

namespace App\Repository;

use App\Entity\Basket;
use App\Entity\Credit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CreditRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Credit::class);
    }

    public function findByProducers(array $producers): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.producer', 'producer')
            ->where('c.producer IN (:producers)')
            ->andWhere('c.deleted = 0')
            ->setParameter('producers', $producers)
            ->orderBy('producer.order', 'asc')
            ->addOrderBy('producer.lastname', 'asc')
            ->addOrderBy('c.date', 'desc')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByAmount(): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.member', 'm')
            ->join('c.producer', 'p')
            ->select('m.id as member_id')
            ->addSelect('p.id as producer_id')
            ->addSelect('c.id as credit_id')
            ->addSelect('c.currentAmount as amount')
            ->andWhere('c.deleted = 0')
            ->andWhere('c.active = 1')
            ->andWhere('c.currentAmount > 0')
            ->getQuery()
            ->getArrayResult();
    }

    public function updateAmount(array $credits)
    {
        $entityManager = $this->getEntityManager();
        foreach($credits as $credit) {
            $sql = '
                UPDATE amap_credit
                SET amap_credit.current_amount = :current_amount
                WHERE amap_credit.id = :id
            ;';
            $statement = $entityManager->getConnection()->prepare($sql);
            $statement->bindValue('current_amount', $credit['currentAmount']);
            $statement->bindValue('id', $credit['id']);
            $statement->execute();
        }
    }
}
