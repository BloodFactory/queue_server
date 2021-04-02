<?php

namespace App\Repository;

use App\Entity\QueueRestDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QueueRestDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method QueueRestDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method QueueRestDay[]    findAll()
 * @method QueueRestDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueueRestDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QueueRestDay::class);
    }

    // /**
    //  * @return QueueRestDay[] Returns an array of QueueRestDay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QueueRestDay
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
