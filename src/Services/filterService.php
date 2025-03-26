<?php

namespace App\Services;

use App\Entity\Participant;
use App\Model\SortieFiltersModel;

class filterService
{
    public function filterSorties(array $data, SortieFiltersModel $filtersObj, Participant $participant): array {
        $returnArray = [];
        foreach ($data as $sortie) {
            if ($filtersObj->isSortieQueJOrganise() && $sortie->getOrganisateur()->getId() != $participant->getId()) {
                continue;
            }
            $returnArray[] = $sortie;
        }
        return $returnArray;
    }
}