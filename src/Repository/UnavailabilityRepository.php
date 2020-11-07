<?php

namespace App\Repository;

use App\Doctrine\DateKey;
use App\Entity\Unavailability;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UnavailabilityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Unavailability::class);
    }

    public function remove(User $member, array $dates)
    {
        if (!empty($dates)) {
            $this->createQueryBuilder('u')
                ->delete()
                ->where('u.member = :member')
                ->andWhere('u.date IN (:dates)')
                ->setParameters([
                    'member' => $member,
                    'dates' => $dates,
                ])
                ->getQuery()
                ->execute();
        }
    }

    public function findByDate(DateKey $date): array
    {
        return $this->createQueryBuilder('u')
            ->select('m.id')
            ->join('u.member', 'm')
            ->where('u.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getArrayResult();
    }

    public function findByUserAndDate(User $member, \DateTime $date): ?Unavailability
    {
        return $this->createQueryBuilder('u')
            ->where('u.date = :date')
            ->andWhere('u.member = :member')
            ->setParameters([
                'date' => $date->format('Y-m-d'),
                'member' => $member,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
