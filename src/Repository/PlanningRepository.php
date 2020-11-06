<?php

namespace App\Repository;

use App\Entity\Planning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PlanningRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Planning::class);
    }

    public function findPeriodByPlanning(Planning $planning): ?array
    {
        return $this->createQueryBuilder('p')
            ->select('MIN(e.date) as min')
            ->addSelect('MAX(e.date) as max')
            ->innerJoin('p.elements', 'e')
            ->where('p = :planning')
            ->andWhere('p.deleted = 0')
            ->setParameters([
                'planning' => $planning,
            ])
            ->getQuery()
            ->getScalarResult()
            ;
    }
}
