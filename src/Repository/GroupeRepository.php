<?php

namespace App\Repository;

use App\Entity\Groupe;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Groupe>
 */
class GroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Groupe::class);
    }

    public function getGroupesApparus(Participant $user):array {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.participants', 'p')
            ->Where('p = :user')
            ->AndWhere('g.createur != :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function getGroupesPossedes(Participant $user):array {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.createur', 'p')
            ->Where('p = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Site[] Returns an array of Site objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

}
