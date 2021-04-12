<?php

namespace App\Repository;

use App\Entity\OrganizationService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrganizationService|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganizationService|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganizationService[]    findAll()
 * @method OrganizationService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationService::class);
    }
}
