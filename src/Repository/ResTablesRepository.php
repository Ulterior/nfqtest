<?php

namespace App\Repository;

use App\Entity\ResTables;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ResTables|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResTables|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResTables[]    findAll()
 * @method ResTables[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResTablesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResTables::class);
    }

    // /**
    //  * @return ResTables[] Returns an array of ResTables objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResTables
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
