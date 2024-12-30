<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function searchEngine(string $query): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.name LIKE :query')
            ->orWhere('s.description LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult()
        ;
    }

       /**
        * @return Service[] Returns an array of Service objects
        */
       public function findByIdUp($value): array
       {
           return $this->createQueryBuilder('s')
               ->andWhere('s.id > :val')
               ->setParameter('val', $value)
               ->orderBy('s.id', 'DESC')
               //->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }

    //    public function findOneBySomeField($value): ?Service
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
