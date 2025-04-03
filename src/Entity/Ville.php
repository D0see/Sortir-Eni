<?php

namespace App\Entity;

use App\Repository\VilleRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
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

    #[ORM\Column(length: 5, nullable: false)]
    #[Assert\NotBlank(message: "Le nom du groupe ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: "/^[0-9]{5}$/",
        message: "Le code postal doit être composé de 5 chiffres."
    )]
    private ?string $codePostal = null;

    /**
     * @var Collection<int, Lieu>
     */
    #[ORM\OneToMany(targetEntity: Lieu::class, mappedBy: 'ville')]
    private Collection $lieux;

    public function __construct()
    {
        $this->lieux = new ArrayCollection();
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

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection<int, Lieu>
     */
    public function getLieux(): Collection
    {
        return $this->lieux;
    }

    public function addLieux(Lieu $lieux): static
    {
        if (!$this->lieux->contains($lieux)) {
            $this->lieux->add($lieux);
            $lieux->setVille($this);
        }

        return $this;
    }

    public function removeLieux(Lieu $lieux): static
    {
        if ($this->lieux->removeElement($lieux)) {
            // set the owning side to null (unless already changed)
            if ($lieux->getVille() === $this) {
                $lieux->setVille(null);
            }
        }

        return $this;
    }
}
