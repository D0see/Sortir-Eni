<?php

namespace App\Services;

use App\Entity\Participant;
use App\Model\SortieFiltersModel;

class filterService
{
    public function filterSorties(array $data, SortieFiltersModel $filtersObj, Participant $participant): array {
        $returnArray = [];
        foreach ($data as $sortie) {
            //Sortie Organisée par l'utilisateur
            if ($filtersObj->isSortieQueJOrganise() &&
                $sortie->getOrganisateur()->getId() != $participant->getId()) continue;
            //Sortie ou l'utilisateur est inscrit
            if ($filtersObj->isSortieOuJeSuisInscrit() &&
                !in_array($participant->getId(), array_map(fn($par): int => $par->getId(), iterator_to_array($sortie->getParticipants())))) continue;

            $returnArray[] = $sortie;
        }
        return $returnArray;
    }
}