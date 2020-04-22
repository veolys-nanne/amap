<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByRole(string $role, User $parent = null): array
    {
        return $this->getQueryBuilderForFindByRole($role, $parent)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByRoleAndActive(string $role, User $parent = null): array
    {
        return $this->getQueryBuilderForFindByRoleAndActive($role, $parent)
            ->getQuery()
            ->getResult();
    }

    public function findByRoleOrNoRole(string $role, User $parent = null): array
    {
        return $this->getQueryBuilderForFindByRole($role, $parent)
            ->where("u.roles LIKE :role OR u.roles='a:0:{}'")
            ->andWhere('u.deleted = 0')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getQueryBuilderForFindByRoleAndActive(string $role, User $parent = null)
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder
            ->where('u.roles LIKE :role')
            ->andWhere('u.deleted = 0')
            ->andWhere('u.active = 1')
            ->setParameter('role', '%"'.$role.'"%')
            ->orderBy('u.lastname', 'asc');
        if (null !== $parent) {
            $queryBuilder
                ->andWhere('u.parent = :parent')
                ->setParameter('parent', $parent);
        }

        return $queryBuilder;
    }

    public function findOnlyMemberActive()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles = \'a:1:{i:0;s:11:"ROLE_MEMBER";}\'')
            ->andWhere('u.deleted = 0')
            ->andWhere('u.active = 1')
            ->orderBy('u.lastname', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function getQueryBuilderForFindByRole(string $role, User $parent = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder
            ->where('u.roles LIKE :role')
            ->andWhere('u.deleted = 0')
            ->setParameter('role', '%"'.$role.'"%');
        if (null !== $parent && !in_array('ROLE_ADMIN', $parent->getRoles())) {
            $queryBuilder
                ->andWhere('u.parent = :parent')
                ->setParameter('parent', $parent);
        }

        return $queryBuilder
            ->orderBy('u.order', 'asc')
            ->addOrderBy('u.lastname', 'asc')
            ;
    }

    public function findUserWithNoBasket(array $models)
    {
        if (count($models) > 0) {
            return $this->createQueryBuilder('u')
                ->leftJoin('u.baskets', 'basket', Join::WITH, 'basket.parent IN (:models)')
                ->where('u.roles LIKE :role')
                ->andWhere('u.deleted  = 0')
                ->setParameters([
                    'role' => '%"ROLE_MEMBER"%',
                    'models' => $models,
                ])
                ->groupBy('u.id')
                ->having('count(basket.id) = 0')
                ->getQuery()
                ->getResult()
                ;
        }

        return [];
    }

    public function findMaxOrder(): ?int
    {
        return $this->createQueryBuilder('u')
            ->select('MAX(u.order) as max')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_PRODUCER"%')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function findByDeleveries(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.deleveries IS NOT NULL')
            ->andWhere("u.deleveries <> 'a:0:{}'")
            ->andWhere('u.deleted = 0')
            ->getQuery()
            ->getResult()
            ;
    }
}
