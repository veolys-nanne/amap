<?php

namespace App\Repository;

use App\Entity\AvailabilityScheduleElement;
use App\Entity\Planning;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AvailabilityScheduleElementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AvailabilityScheduleElement::class);
    }

    public function findByMemberAndByState(User $member, $state): array
    {
        return $this->createQueryBuilder('ase')
            ->innerJoin('ase.availabilitySchedule', 'asch')
            ->innerJoin('asch.planning', 'p')
            ->where('asch.member = :member')
            ->andWhere('p.deleted = 0')
            ->andWhere('p.state = :state')
            ->orderBy('ase.date', 'asc')
            ->setParameters([
                'member' => $member,
                'state' => $state,
            ])
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByPlanningAndDate(Planning $planning, \DateTime $date): array
    {
        return $this->createQueryBuilder('ase')
            ->innerJoin('ase.availabilitySchedule', 'asch')
            ->innerJoin('asch.member', 'm')
            ->where('asch.planning = :planning')
            ->andWhere('ase.date = :date')
            ->orderBy('m.order', 'asc')
            ->addOrderBy('m.lastname', 'asc')
            ->setParameters([
                'planning' => $planning,
                'date' => $date,
            ])
            ->getQuery()
            ->getResult()
            ;
    }
}
