<?php

namespace App\Repository;

use App\Entity\PlanningElement;
use App\Entity\Planning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PlanningElementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlanningElement::class);
    }

    public function findByActivePlanning(Planning $planning = null): ?array
    {
        $qb = $this->createQueryBuilder('pe')
            ->select('pe.date')
            ->join('pe.planning', 'p')
            ->where('p.state IN (:states)')
            ->andWhere('pe.date >= :date')
            ->andWhere('p.deleted = 0');
        $parameters = [
            'date' => (new \DateTime())->format('Y-m-d'),
            'states' => [
                Planning::STATE_OPEN,
                Planning::STATE_CLOSE,
                Planning::STATE_ONLINE,
            ],
        ];

        if (null != $planning && $planning->getId()) {
            $qb->andWhere('pe.planning != :planning');
            $parameters['planning'] = $planning->getId();
        }

        return $qb
            ->setParameters($parameters)
            ->getQuery()
            ->getScalarResult()
            ;
    }

    public function findByClosedPlanning(): ?array
    {
        return $this->createQueryBuilder('pe')
            ->select('pe.date')
            ->join('pe.planning', 'p')
            ->where('p.state IN (:states)')
            ->andWhere('pe.date >= :date')
            ->andWhere('p.deleted = 0')
            ->setParameters([
                'date' => (new \DateTime())->format('Y-m-d'),
                'states' => [
                    Planning::STATE_CLOSE,
                    Planning::STATE_ONLINE,
                ],
            ])
            ->getQuery()
            ->getScalarResult()
            ;
    }

    public function findByOnline(): array
    {
        return $this->createQueryBuilder('pe')
            ->join('pe.planning', 'p')
            ->join('pe.members', 'm')
            ->where('p.state = :state')
            ->andWhere('pe.date >= :date')
            ->andWhere('p.deleted = 0')
            ->setParameters([
                'date' => (new \DateTime())->format('Y-m-d'),
                'state' => Planning::STATE_ONLINE,
            ])
            ->orderBy('pe.date')
            ->getQuery()
            ->getResult();
    }
}
