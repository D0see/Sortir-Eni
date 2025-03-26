<?php

namespace App\Services;

use App\Entity\Participant;
use App\Model\SortieFiltersModel;

class filterService
{
    public function filterSorties(array $data, SortieFiltersModel $filtersObj, Participant $participant): array {
        foreach ($data as $sortie) {

        }
        return $data;
    }
}