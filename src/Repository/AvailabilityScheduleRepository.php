<?php

namespace App\Repository;

use App\Entity\AvailabilitySchedule;
use App\Entity\Planning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AvailabilityScheduleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AvailabilitySchedule::class);
    }

    public function findMemberByEmptyAvailability(): array
    {
        return $this->createQueryBuilder('asch')
            ->select('p.id as planning')
            ->addSelect('u.email as email')
            ->addSelect('u.broadcastList as broadcastList')
            ->innerJoin('asch.planning', 'p')
            ->innerJoin('asch.member', 'u')
            ->innerJoin('asch.elements', 'ase')
            ->where('p.state = :state')
            ->andWhere('p.deleted = 0')
            ->having('SUM(ase.isAvailable) = 0')
            ->groupBy('asch.planning')
            ->addGroupBy('asch.member')
            ->setParameters([
                'state' => Planning::STATE_OPEN,
            ])
            ->getQuery()
            ->getArrayResult();
    }
}
