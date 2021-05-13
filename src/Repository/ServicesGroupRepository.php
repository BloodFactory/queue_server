<?php

namespace App\Repository;

use App\Entity\ServicesGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServicesGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServicesGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServicesGroup[]    findAll()
 * @method ServicesGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServicesGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServicesGroup::class);
    }
}
