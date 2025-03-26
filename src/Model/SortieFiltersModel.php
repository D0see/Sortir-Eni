<?php

namespace App\Model;

use App\Entity\Site;

class SortieFiltersModel
{
    private ?Site $site = null;
    private ?string $contenu = null;

    private ?\DateTime $debut = null;
    private ?\DateTime $fin = null;

    private bool $sortieQueJOrganise = false;

    private bool $sortieOuJeSuisInscrit = false;

    private bool $sortieOuJeNeSuisPasInscrit = false;

    private bool $sortiePassees = false;

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): void
    {
        $this->site = $site;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): void
    {
        $this->contenu = $contenu;
    }

    public function getDebut(): ?\DateTime
    {
        return $this->debut;
    }

    public function setDebut(?\DateTime $debut): void
    {
        $this->debut = $debut;
    }

    public function getFin(): ?\DateTime
    {
        return $this->fin;
    }

    public function setFin(?\DateTime $fin): void
    {
        $this->fin = $fin;
    }

    public function isSortieQueJOrganise(): bool
    {
        return $this->sortieQueJOrganise;
    }

    public function setSortieQueJOrganise(bool $sortieQueJOrganise): void
    {
        $this->sortieQueJOrganise = $sortieQueJOrganise;
    }

    public function isSortieOuJeSuisInscrit(): bool
    {
        return $this->sortieOuJeSuisInscrit;
    }

    public function setSortieOuJeSuisInscrit(bool $sortieOuJeSuisInscrit): void
    {
        $this->sortieOuJeSuisInscrit = $sortieOuJeSuisInscrit;
    }

    public function isSortieOuJeNeSuisPasInscrit(): bool
    {
        return $this->sortieOuJeNeSuisPasInscrit;
    }

    public function setSortieOuJeNeSuisPasInscrit(bool $sortieOuJeNeSuisPasInscrit): void
    {
        $this->sortieOuJeNeSuisPasInscrit = $sortieOuJeNeSuisPasInscrit;
    }

    public function isSortiePassees(): bool
    {
        return $this->sortiePassees;
    }

    public function setSortiePassees(bool $sortiePassees): void
    {
        $this->sortiePassees = $sortiePassees;
    }
}