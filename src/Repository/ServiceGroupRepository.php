<?php

namespace App\Repository;

use App\Entity\ServiceGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceGroup[]    findAll()
 * @method ServiceGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceGroup::class);
    }

    // /**
    //  * @return ServiceGroup[] Returns an array of ServiceGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ServiceGroup
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
