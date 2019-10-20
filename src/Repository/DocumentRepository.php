<?php

namespace App\Repository;

use App\Entity\Basket;
use App\Entity\Credit;
use App\Entity\Document;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function findByNameAndRole(string $name, string $role): Document
    {
        return $this->createQueryBuilder('d')
            ->where('d.name = :name')
            ->andWhere('d.role = :role OR d.role IS NULL')
            ->andWhere('d.deleted = 0')
            ->setParameters([
                'name' => $name,
                'role' => $role,
            ])
            ->getQuery()
            ->getSingleResult();
    }
}
