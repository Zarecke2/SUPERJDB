<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

   /**
    * @return Post[] Returns an array of Post objects
    */
    public function findCommentaire($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.related_to = :val')
            ->setParameter('val', $value)
            ->orderBy('p.date_post', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findTitre($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $value)
            ->select('p.nom_post as titre, p.texte as texte')
            ->getQuery()
            ->getResult()
        ;
    }

    public function postJdbOrdered($value){
        return $this->createQueryBuilder('p') 
                    ->join('p.jdbPost', 'jdb')
                    ->where('jdb.id = :value')
                    ->setParameter('value', $value)
                    ->orderBy('p.date_post', 'desc')
                    ->getQuery()
                    ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
