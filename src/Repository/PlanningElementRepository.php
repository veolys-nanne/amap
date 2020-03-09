<?php

namespace App\Repository;

use App\Entity\PlanningElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PlanningElementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlanningElement::class);
    }

    public function findMembersByDate(\DateTime $date): ?array
    {
        return $this->createQueryBuilder('pe')
            ->innerJoin('pe.planning', 'p')
            ->innerJoin('pe.members', 'u')
            ->select('pe.date as date')
            ->addSelect('pe.id as id')
            ->addSelect('u.email as email')
            ->addSelect('u.broadcastList as broadcastList')
            ->where('p.deleted = 0')
            ->andWhere('pe.date = :date')
            ->setParameters([
                'date' => $date->setTime(0, 0, 0),
            ])
            ->getQuery()
            ->getResult();
    }
}
