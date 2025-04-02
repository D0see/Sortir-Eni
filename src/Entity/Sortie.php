<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le pseudo est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le pseudo doit faire au moins {{ limit }} caractères",
        maxMessage: "Le pseudo ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date et l'heure de début sont obligatoires")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date de début ne peut pas être inférieure à aujourd'hui"
    )]
    private ?\DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La durée est obligatoire")]
    #[Assert\Positive(message: "La durée doit être supérieure à 0")]
    #[Assert\Type(
        type: "integer",
        message: "La durée doit être un nombre entier"
    )]
    private ?int $dureeEnHeures = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date limite d'inscription est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date limite d'inscription ne peut pas être inférieure à aujourd'hui"
    )]
    #[Assert\Expression(
        "this.getDateLimiteInscription() <= this.getDateHeureDebut()",
        message: "La date limite d'inscription doit être avant la date de début"
    )]
    private ?\DateTimeInterface $dateLimiteInscription = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre maximum d'inscriptions est obligatoire")]
    #[Assert\Positive(message: "Le nombre maximum d'inscriptions doit être supérieur à 0")]
    #[Assert\Type(
        type: "integer",
        message: "Le nombre d'inscriptions maximum doit être un nombre entier"
    )]
    private ?int $nbInscriptionsMax = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Les informations sur la sortie sont obligatoires")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Les informations ne peuvent pas dépasser {{ limit }} caractères"
    )]
    private ?string $infosSortie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date d'ouverture est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date d'ouverture ne peut pas être inférieure à aujourd'hui"
    )]
    #[Assert\Expression(
        "this.getDateLimiteInscription() <= this.getDateHeureDebut()",
        message: "La date d'ouverture doit être avant la date limte d'inscription"
    )]
    private ?\DateTimeInterface $dateOuverture = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage:"Le motif d'annulation doit faire au moins {{ limit }} caractères",
        maxMessage: "Le motif d'annulation ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $motifAnnulation = null;


    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $etat = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieu $lieu = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, mappedBy: 'sortiesParticipees')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'sortiesOrganisees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $organisateur = null;



    #[Assert\Callback]
    public function validateDateOuverture(ExecutionContextInterface $context, $payload): void
    {
        if ($this->dateOuverture !== null && $this->dateLimiteInscription !== null) {
            if ($this->dateOuverture > $this->dateLimiteInscription) {
                $context->buildViolation("La date d'ouverture doit être avant ou égale à la date limite d'inscription")
                    ->atPath('dateOuverture')
                    ->addViolation();
            }
        }
    }
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

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDureeEnHeures(): ?int
    {
        return $this->dureeEnHeures;
    }

    public function setDureeEnHeures(int $dureeEnHeures): static
    {
        $this->dureeEnHeures = $dureeEnHeures;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): static
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): static
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getDateOuverture(): ?\DateTimeInterface
    {
        return $this->dateOuverture;
    }

    public function setDateOuverture(\DateTimeInterface $dateOuverture): static
    {
        $this->dateOuverture = $dateOuverture;

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): static
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

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
            $participant->addSortiesParticipee($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeSortiesParticipee($this);
        }

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

}
