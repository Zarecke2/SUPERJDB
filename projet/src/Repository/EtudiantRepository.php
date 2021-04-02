<?php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Etudiant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etudiant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etudiant[]    findAll()
 * @method Etudiant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }



    public function FindAllNetud()
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.num_etudiant as netud')
            ->getQuery();
            $result = $qb->execute();
            $result = array_map('current', $result);
            return $result;
    }

    public function findLogged($value){
        return $this->createQueryBuilder('e')
             ->where("e.login = :value")
             ->setParameter('value', $value)
             ->getQuery()
             ->getResult();
    }

    public function findJournalDeBord($value){
        return $this->createQueryBuilder('etudiant')
                    ->join('etudiant.equipeEtudiant', 'equipe')
                    ->join('equipe.jdbEquipe', 'jdb')
                    /*->where('etudiant.id = :value')
                    ->setParameter('value', $value)*/
                    ->getQuery()
                    ->getResult();
    }

    public function findOneByIdAndName($id, $nom){
        return $this->createQueryBuilder('e')
                    ->where('e.id = :id')
                    ->andWhere('e.nom_etudiant = :nom')
                    ->setParameter('id', $id)
                    ->setParameter('nom', $nom)
                    ->getQuery()
                    ->getResult();
    }

    


    // /**
    //  * @return Etudiant[] Returns an array of Etudiant objects
    //  */
    /*

    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Etudiant
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
