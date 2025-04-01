<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Model\SortieFiltersModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function getNonArchivedSorties():array {
        $threshold = new \DateTime();
        $threshold->modify('- 1 month');
        return $this->createQueryBuilder('s')
            ->Where('s.dateHeureDebut > :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getResult();
    }

    public function getSortieFiltered(Participant $user, SortieFiltersModel $filtersObj):array {

        $threshold = new \DateTime();
        $threshold->modify('- 1 month');
         $filteredQuery = $this->createQueryBuilder('s')
             ->Where('s.dateHeureDebut > :threshold')
             ->setParameter('threshold', $threshold)
            ->leftJoin('s.participants', 'p')->addSelect('p')
            ->leftJoin('s.etat', 'e')->addSelect('e')
            ->leftJoin('s.site', 'si')->addSelect('si');

        //Sortie Organisée par l'utilisateur
        if ($filtersObj->isSortieQueJOrganise()) {
            $filteredQuery
                ->andwhere('s.organisateur = :user')
                ->setParameter('user', $user);
        }

        //Sortie ou l'utilisateur est inscrit
        if ($filtersObj->isSortieOuJeNeSuisPasInscrit()) {
            $filteredQuery
                ->andWhere(':user not member of s.participants')
                ->setParameter('user', $user);
        }

        //Sortie ou l'utilisateur n'est pas inscrit
         if ($filtersObj->isSortieOuJeSuisInscrit()) {
            $filteredQuery
                ->andWhere(':user member of s.participants')
                ->setParameter('user', $user);
        }

        //Sortie passées
        if ($filtersObj->isSortiePassees()) {
            $filteredQuery
                ->andwhere('e.libelle = :libelle')
                ->setParameter('libelle', 'passée');
        }

        // filtre par contenu
        if ($filtersObj->getContenu()) {
            $filteredQuery
                ->andwhere('UPPER(s.infosSortie) LIKE UPPER(:contenu) OR UPPER(s.nom) LIKE UPPER(:contenu)')
                ->setParameter('contenu', '%'.$filtersObj->getContenu().'%');
        }

        // filtre par site
        if ($filtersObj->getSite()) {
            $filteredQuery
                ->andwhere('si = :inputtedSite')
                ->setParameter('inputtedSite', $filtersObj->getSite());
        }

        // Si le filtre début est après la date d'ouverture des inscriptions
        if ($filtersObj->getDebut()) {
            $filteredQuery
                ->andwhere('s.dateHeureDebut > :dateDebut')
                ->setParameter('dateDebut', $filtersObj->getDebut());
        }

        // Si le filtre fin est avant la fin de la sortie
        //TODO add sortie.duree to dateHeureDebut before comparing
        if ($filtersObj->getFin()) {
            $filteredQuery
                ->andwhere('s.dateHeureDebut < :dateFin')
                ->setParameter('dateFin', $filtersObj->getFin());
        }


        return $filteredQuery->getQuery()->getResult();
    }

    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
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

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
