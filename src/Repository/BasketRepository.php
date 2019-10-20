<?php

namespace App\Repository;

use App\Entity\Basket;
use App\Entity\Product;
use App\Entity\User;
use App\Form\SynthesesType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BasketRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Basket::class);
    }

    public function findModel(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.parent IS NULL')
            ->andWhere('b.deleted = 0')
            ->orderBy('b.date', 'desc')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneByParentAndUser(Basket $parent, User $user): ?Basket
    {
        return $this->createQueryBuilder('b')
            ->where('b.parent = :parent')
            ->andWhere('b.deleted = 0')
            ->andWhere('b.user = :user')
            ->setParameters([
                'parent' => $parent,
                'user' => $user,
            ])
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findByFrozenAndModel(int $frozen): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.frozen = :frozen')
            ->andWhere('b.parent IS NULL')
            ->andWhere('b.deleted = 0')
            ->setParameters([
                'frozen' => $frozen,
            ])
            ->orderBy('b.date', 'asc')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findCreditByDateForProducer(
        \DateTime $start,
        \DateTime $end
    ): array {
        return $this->createQueryBuilder('b')
            ->join('b.creditBasketAmountCollection', 'cbac')
            ->join('cbac.credit', 'c')
            ->join('c.member', 'm')
            ->join('c.producer', 'producer')
            ->select('producer.id as id')
            ->addSelect('SUM(coalesce(cbac.amount, 0)) as value')
            ->addSelect('CONCAT(m.lastname, \' \', m.firstname) as name')
            ->addSelect('c.object as object')
            ->addSelect('c.totalAmount as totalAmount')
            ->addSelect('c.currentAmount as currentAmount')
            ->where('b.date BETWEEN :start AND :end')
            ->andWhere('b.parent IS NOT NULL')
            ->andWhere('b.deleted = 0')
            ->setParameters([
                'start' => $start->setTime(0, 0, 0),
                'end' => $end->setTime(23, 59, 59),
            ])
            ->orderBy('c.date', 'asc')
            ->groupBy('c.id')
            ->getQuery()
            ->getScalarResult()
            ;
    }

    public function findCreditByDateForMember(
        \DateTime $start,
        \DateTime $end
    ): array {
        return $this->createQueryBuilder('b')
            ->join('b.creditBasketAmountCollection', 'cbac')
            ->join('cbac.credit', 'c')
            ->join('c.member', 'm')
            ->join('c.producer', 'producer')
            ->select('m.id as id')
            ->addSelect('SUM(coalesce(cbac.amount, 0)) as value')
            ->addSelect('CONCAT(coalesce(producer.lastname, \'\'), \' \', coalesce(producer.firstname, \'\')) as name')
            ->addSelect('c.object as object')
            ->addSelect('c.totalAmount as totalAmount')
            ->addSelect('c.currentAmount as currentAmount')
            ->where('b.date BETWEEN :start AND :end')
            ->andWhere('b.parent IS NOT NULL')
            ->andWhere('b.deleted = 0')
            ->setParameters([
                'start' => $start->setTime(0, 0, 0),
                'end' => $end->setTime(23, 59, 59),
            ])
            ->orderBy('c.date', 'asc')
            ->groupBy('c.id')
            ->getQuery()
            ->getScalarResult()
            ;
    }

    public function findSumCreditByDate(
        \DateTime $start,
        \DateTime $end
    ): array {
        return $this->createQueryBuilder('b')
            ->join('b.creditBasketAmountCollection', 'cbac')
            ->join('cbac.credit', 'c')
            ->join('c.member', 'm')
            ->join('c.producer', 'producer')
            ->select('producer.id as id')
            ->addSelect('\'Avoir\' as label')
            ->addSelect('SUM(coalesce(cbac.amount, 0)) as value')
            ->where('b.date BETWEEN :start AND :end')
            ->andWhere('b.parent IS NOT NULL')
            ->andWhere('b.deleted = 0')
            ->setParameters([
                'start' => $start->setTime(0, 0, 0),
                'end' => $end->setTime(23, 59, 59),
            ])
            ->groupBy('c.producer')
            ->getQuery()
            ->getScalarResult()
        ;
    }

    public function getColumns(
        \DateTime $start,
        \DateTime $end,
        int $type
    ): array
    {
        switch ($type) {
            case SynthesesType::INVOICE_BY_MEMBER:
                return ['Total', 'Avoir'];
                break;
            case SynthesesType::INVOICE_BY_PRODUCER_BY_MEMBER:
                return ['Total', 'Avoir'];
                break;
            default:
                return array_column($this->createQueryBuilder('b')
                    ->select('b.date')
                    ->where('b.date BETWEEN :start AND :end')
                    ->andWhere('b.deleted = 0')
                    ->setParameters([
                        'start' => $start->setTime(0, 0, 0),
                        'end' => $end->setTime(23, 59, 59),
                    ])
                    ->orderBy('b.date')
                    ->groupBy('b.date')
                    ->getQuery()
                    ->getArrayResult(), 'date');
                break;
        }
    }

    public function getSyntheses(
        \DateTime $start,
        \DateTime $end,
        int $type
    ): array {
        $queryBuilder = $this->createQueryBuilder('b')
            ->select('producer.color as color')
            ->join('b.productQuantityCollection', 'pqc', 'WITH', 'pqc.quantity > 0')
            ->join('b.user', 'user')
            ->join('pqc.product', 'p')
            ->join('p.producer', 'producer')
            ->where('b.date BETWEEN :start AND :end')
            ->andWhere('b.parent IS NOT NULL')
            ->andWhere('b.deleted = 0')
            ->setParameters([
                'start' => $start->setTime(0, 0, 0),
                'end' => $end->setTime(23, 59, 59),
            ]);
        switch ($type) {
            case SynthesesType::INVOICE_BY_MEMBER:
                $queryBuilder
                    ->leftJoin('b.creditBasketAmountCollection', 'cba')
                    ->leftJoin('cba.credit', 'c')
                    ->addSelect('CONCAT(user.lastname, \' \', coalesce(user.firstname, \'\')) as table')
                    ->addSelect('user.email as email')
                    ->addSelect('user.broadcastList as broadcastList')
                    ->addSelect('user.id as id')
                    ->addSelect('CONCAT(producer.lastname, \' \', coalesce(producer.firstname, \'\'), coalesce(CONCAT(\'(ordre: \', producer.payto, \')\'), \'\')) as line')
                    ->addSelect('SUM(coalesce(pqc.price, p.price) * coalesce(pqc.quantity, 0)) as Total')
                    ->addSelect('SUM(coalesce(cba.amount, 0)) as Avoir')
                    ->andWhere('c.producer = p.producer OR c IS NULL')
                    ->orderBy('user.lastname', 'asc')
                    ->addOrderBy('producer.order', 'asc')
                    ->addOrderBy('producer.lastname', 'asc')
                    ->groupBy('producer.id')
                    ->addGroupBy('user.id');
                break;
            case SynthesesType::PRODUCT_BY_PRODUCER:
                $queryBuilder
                    ->addSelect('b.date as column')
                    ->addSelect('CONCAT(producer.lastname, \' \', coalesce(producer.firstname, \'\')) as table')
                    ->addSelect('\'\' as id')
                    ->addSelect('p.name as line')
                    ->addSelect('SUM(coalesce(pqc.quantity, 0)) as value')
                    ->orderBy('producer.order', 'asc')
                    ->addOrderBy('producer.lastname', 'asc')
                    ->addOrderBy('p.order', 'asc')
                    ->addOrderBy('p.name', 'asc')
                    ->addOrderBy('b.date', 'asc')
                    ->groupBy('b.date')
                    ->addGroupBy('p.id');
                break;
            case SynthesesType::PRODUCT_BY_MEMBER:
                $queryBuilder
                    ->addSelect('b.date as column')
                    ->addSelect('CONCAT(user.lastname, \' \', coalesce(user.firstname, \'\')) as table')
                    ->addSelect('user.id as id')
                    ->addSelect('p.name as line')
                    ->addSelect('SUM(coalesce(pqc.quantity, 0)) as value')
                    ->orderBy('user.lastname', 'asc')
                    ->addOrderBy('producer.order', 'asc')
                    ->addOrderBy('producer.lastname', 'asc')
                    ->addOrderBy('p.order', 'asc')
                    ->addOrderBy('p.name', 'asc')
                    ->addOrderBy('b.date', 'asc')
                    ->groupBy('b.date')
                    ->addGroupBy('p.id')
                    ->addGroupBy('user.id');
                break;
            case SynthesesType::INVOICE_BY_PRODUCER:
                $queryBuilder
                    ->addSelect('b.date as column')
                    ->addSelect('CONCAT(producer.lastname, \' \', coalesce(producer.firstname, \'\')) as table')
                    ->addSelect('producer.email as email')
                    ->addSelect('producer.broadcastList as broadcastList')
                    ->addSelect('producer.id as id')
                    ->addSelect('p.name as line')
                    ->addSelect('SUM(coalesce(pqc.quantity, 0)) as value')
                    ->orderBy('producer.order', 'asc')
                    ->addOrderBy('producer.lastname', 'asc')
                    ->addOrderBy('p.order', 'asc')
                    ->addOrderBy('p.name', 'asc')
                    ->addOrderBy('b.date', 'asc')
                    ->groupBy('b.date')
                    ->addGroupBy('p.id');
                break;
            case SynthesesType::INVOICE_BY_PRODUCER_BY_MEMBER:
                $queryBuilder
                    ->join('producer.parent', 'referent')
                    ->leftJoin('b.creditBasketAmountCollection', 'cba')
                    ->addSelect('CONCAT(referent.lastname, \' \', coalesce(referent.firstname, \'\')) as table')
                    ->addSelect('referent.email as email')
                    ->addSelect('referent.broadcastList as broadcastList')
                    ->addSelect('referent.id as id')
                    ->addSelect('CONCAT(user.lastname, \' \', coalesce(user.firstname, \'\')) as line')
                    ->addSelect('CONCAT(producer.lastname, \' \', coalesce(producer.firstname, \'\')) as tbody')
                    ->addSelect('SUM(coalesce(pqc.price, p.price) * coalesce(pqc.quantity, 0)) as Total')
                    ->addSelect('SUM(coalesce(cba.amount, 0)) as Avoir')
                    ->orderBy('producer.order', 'asc')
                    ->addOrderBy('producer.lastname', 'asc')
                    ->addOrderBy('user.lastname', 'asc')
                    ->addOrderBy('referent.lastname', 'asc')
                    ->groupBy('user.id')
                    ->addGroupBy('producer.id');
                break;
        }

        return $queryBuilder
            ->getQuery()
            ->getArrayResult();
    }

    public function getInvoiceByBasket(Basket $basket): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.productQuantityCollection', 'pqc', 'WITH', 'pqc.quantity > 0')
            ->join('b.user', 'user')
            ->join('pqc.product', 'p')
            ->join('p.producer', 'producer')
            ->select('user.id as member_id')
            ->addSelect('b.id as basket_id')
            ->addSelect('producer.id as producer_id')
            ->addSelect('SUM(coalesce(pqc.price, p.price) * coalesce(pqc.quantity, 0)) as invoice')
            ->where('b.parent IS NOT NULL')
            ->andWhere('b.parent = :basket')
            ->setParameters([
                'basket' => $basket,
            ])
            ->groupBy('producer.id')
            ->addGroupBy('user.id')
            ->addGroupBy('b.id')
            ->getQuery()
            ->getArrayResult();
    }

    public function findByUserAndDate(
        User $user,
        \DateTime $start,
        \DateTime $end
    ): array {
        return $this->createQueryBuilder('b')
            ->where('b.date BETWEEN :start AND :end')
            ->andWhere('b.parent IS NOT NULL')
            ->andWhere('b.deleted = 0')
            ->andWhere('b.user = :user')
            ->setParameters([
                'start' => $start->setTime(0, 0, 0),
                'end' => $end->setTime(23, 59, 59),
                'user' => $user,
            ])
            ->getQuery()
            ->getResult();
    }

    public function findByParentAndFrozen(Basket $parent, int $frozen): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.parent = :parent')
            ->andWhere('b.frozen = :frozen')
            ->setParameters([
                'parent' => $parent,
                'frozen' => $frozen,
            ])
            ->getQuery()
            ->getResult();
    }

    public function isProductInActiveBasket(Product $product): bool
    {
        return $this->createQueryBuilder('b')
            ->select('count(b.id) as count')
            ->join('b.productQuantityCollection', 'pqc')
            ->where('b.parent IS NOT NULL')
            ->andWhere('b.frozen = 0')
            ->andWhere('b.deleted = 0')
            ->andWhere('pqc.product = :product')
            ->andWhere('pqc.quantity > 0')
            ->setParameters([
                'product' => $product,
            ])
            ->getQuery()
            ->getSingleScalarResult() != 0;
    }

    public function updateFrozenByParent($isFrozen, Basket $basket) {
        $this->createQueryBuilder('b')
            ->update()
            ->set('b.frozen', ':isFrozen')
            ->where('b.parent = :basket')
            ->setParameters([
                'basket' => $basket,
                'isFrozen' => $isFrozen,
            ])
            ->getQuery()
            ->execute();
    }
}
