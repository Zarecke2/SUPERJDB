<?php

namespace App\Repository;

use App\Entity\JournalDeBord;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JournalDeBord|null find($id, $lockMode = null, $lockVersion = null)
 * @method JournalDeBord|null findOneBy(array $criteria, array $orderBy = null)
 * @method JournalDeBord[]    findAll()
 * @method JournalDeBord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JournalDeBordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JournalDeBord::class);
    }

    

    // /**
    //  * @return JournalDeBord[] Returns an array of JournalDeBord objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JournalDeBord
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
