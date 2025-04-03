<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PSEUDO', fields: ['pseudo'])]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, nullable: false)]
    #[Assert\NotBlank(message: "Le nom du groupe ne peut pas être vide.")]
    private ?string $pseudo = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Le nom du groupe ne peut pas être vide.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Le nom du groupe ne peut pas être vide.")]
    private ?string $prenom = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $resetToken = null;


    #[ORM\Column(length: 10, nullable: false)]
    #[Assert\NotBlank(message: "Le nom du groupe ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: "/^(\+?[0-9]{1,3})?([-. ]?[0-9]{2,4}){3,5}$/",
        message: "Le numéro de téléphone n'est pas valide."
    )]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Le nom du groupe ne peut pas être vide.")]
    #[Assert\Email(
        message: "L'email '{{ value }}' n'est pas valide.",
        mode: "html5"
    )]
    private ?string $mail = null;

    #[ORM\Column]
    private ?bool $administrateur = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    #[ORM\Column]
    private ?bool $actif = null;

    //aq vamateb axal xazss

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maPhoto=null;


    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
    private Collection $sortiesParticipees;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'organisateur')]
    private Collection $sortiesOrganisees;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\ManyToMany(targetEntity: Groupe::class, mappedBy: 'participants')]
    private Collection $groupes;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\OneToMany(targetEntity: Groupe::class, mappedBy: 'createur')]
    private Collection $groupeCrees;

    public function __construct()
    {
        $this->sortiesParticipees = new ArrayCollection();
        $this->sortiesOrganisees = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->groupeCrees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }


    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTimeInterface $resetTokenExpiresAt): self
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): static
    {
        $this->roles[] = $role;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): static
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getMaPhoto(): ?string
    {
        return $this->maPhoto;
    }

    public function setMaPhoto(?string $maPhoto): static
    {
        $this->maPhoto = $maPhoto;
        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesParticipees(): Collection
    {
        return $this->sortiesParticipees;
    }

    public function addSortiesParticipee(Sortie $sortiesParticipee): static
    {
        if (!$this->sortiesParticipees->contains($sortiesParticipee)) {
            $this->sortiesParticipees->add($sortiesParticipee);
        }

        return $this;
    }

    public function removeSortiesParticipee(Sortie $sortiesParticipee): static
    {
        $this->sortiesParticipees->removeElement($sortiesParticipee);

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees->add($sortiesOrganisee);
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

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

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): static
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->addParticipant($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            $groupe->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupeCrees(): Collection
    {
        return $this->groupeCrees;
    }

    public function addGroupeCree(Groupe $groupeCree): static
    {
        if (!$this->groupeCrees->contains($groupeCree)) {
            $this->groupeCrees->add($groupeCree);
            $groupeCree->setCreateur($this);
        }

        return $this;
    }

    public function removeGroupeCree(Groupe $groupeCree): static
    {
        if ($this->groupeCrees->removeElement($groupeCree)) {
            // set the owning side to null (unless already changed)
            if ($groupeCree->getCreateur() === $this) {
                $groupeCree->setCreateur(null);
            }
        }

        return $this;
    }
}
