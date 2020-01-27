<?php

namespace App\Repository;

use App\Entity\MailLog;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MailLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MailLog::class);
    }

    public function findByRecipients(User $user): array
    {
        return $this->createQueryBuilder('ml')
            ->where(':user MEMBER OF ml.recipients')
            ->andWhere('ml.deleted = 0')
            ->setParameters([
                'user' => $user,
            ])
            ->getQuery()
            ->getResult();
    }
}
