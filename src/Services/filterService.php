<?php

namespace App\Services;

use App\Entity\Participant;
use App\Model\SortieFiltersModel;
use DateInterval;
use DateTime;

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

            //Sortie ou l'utilisateur n'est pas inscrit
            if ($filtersObj->isSortieOuJeNeSuisPasInscrit() &&
                in_array($participant->getId(), array_map(fn($par): int => $par->getId(), iterator_to_array($sortie->getParticipants())))) continue;

            //Sorties passées
            if ($filtersObj->isSortiePassees()) {
                $now = new DateTime();
                if ($sortie->getDateHeureDebut()->modify("+ {$sortie->getDureeEnHeures()} hours") > $now) continue;
            }

            // filtre par contenu
            if ($filtersObj->getContenu()) {
                $str = $filtersObj->getContenu();
                $pattern = "/{$str}/i";
                if (!preg_match($pattern, $sortie->getNom()) &&
                    !preg_match($pattern, $sortie->getInfosSortie()) &&
                    !preg_match($pattern, $sortie->getLieu()->getNom()) &&
                    !preg_match($pattern, $sortie->getSite()->getNom())) continue;
            }

            // filtre par site
            if ($filtersObj->getSite() &&
                $sortie->getSite()->getNom() != $filtersObj->getSite()->getNom()) continue;

            // Si le filtre début est après la date d'ouverture des inscriptions
            if ($filtersObj->getDebut() &&
                $sortie->getDateOuverture() < $filtersObj->getDebut()) continue;


            // Si le filtre fin est avant la fin de la sortie
            if ($filtersObj->getFin() &&
                $sortie->getDateHeureDebut()->modify("+ {$sortie->getDureeEnHeures()} hours") > $filtersObj->getFin()) continue;

            $returnArray[] = $sortie;

        }
        return $returnArray;
    }
}