<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Le nom du groupe ne peut pas être vide.")]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: "Le nom du groupe doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le nom du groupe ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;


    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: 'groupes')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'groupeCrees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $createur = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getCreateur(): ?Participant
    {
        return $this->createur;
    }

    public function setCreateur(?Participant $createur): static
    {
        $this->createur = $createur;

        return $this;
    }
}
