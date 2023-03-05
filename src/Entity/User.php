<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("utilisateur")]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups("utilisateur")]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups("utilisateur")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups("utilisateur")]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups("utilisateur")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups("utilisateur")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Groups("utilisateur")]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reclamation::class)]
    #[Groups("utilisateur")]
    private Collection $Reclamations;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Commande::class)]
    #[Groups("utilisateur")]
    private Collection $commandes;


    #[ORM\OneToOne(mappedBy: 'utilisateur',cascade:['persist','remove'], targetEntity: Panier::class)]
    #[Groups("utilisateur")]
    private $panier;

    public function __construct()
    {
        $this->Reclamations = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->Reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->Reclamations->contains($reclamation)) {
            $this->Reclamations->add($reclamation);
            $reclamation->setUser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->Reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUser() === $this) {
                $reclamation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUtilisateur() === $this) {
                $commande->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

   /* public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }*/
    public function setPanier(?Panier $panier): self
    {
        // unset the owning side of the relation if necessary
        if ($panier === null && $this->panier !== null) {
            $this->panier->setUtilisateur(null);
        }

        // set the owning side of the relation if necessary
        if ($panier !== null && $panier->getUtilisateur() !== $this) {
            $panier->setUtilisateur($this);
        }

        $this->panier = $panier;

        return $this;
    }
    public function __toString()
    {
        return sprintf('%s: %s ', $this->nom, $this->prenom);
    }
}
