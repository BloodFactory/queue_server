<?php

namespace App\Repository;

use App\Entity\QueueDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QueueDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method QueueDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method QueueDay[]    findAll()
 * @method QueueDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueueDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QueueDay::class);
    }

    // /**
    //  * @return QueueDay[] Returns an array of QueueDay objects
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
    public function findOneBySomeField($value): ?QueueDay
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
