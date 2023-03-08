<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface,TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("users")]
    private ?int $id = null;


    

    #[ORM\Column(length: 180, unique: true)]
    #[Groups("users")]
// #[Assert\Type(type: ['alnum'], message: "The adresse '{{ value }}' is not valid")]
#[Assert\Email(
    message: 'The email {{ value }} is not a valid email.',
)]
#[Assert\NotBlank(message:"veuillez remplir le champs")]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups("users")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups("users")]
    private ?string $password = null;


    #[ORM\Column(length: 255)]
    #[Groups("users")]
    #[Assert\NotBlank(message:"veuillez remplir le champs")]
    #[Assert\Type(type: ['alpha'], message: "The name '{{ value }}' is not valid")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups("users")]
    #[Assert\Type(type: ['alpha'], message: "The name '{{ value }}' is not valid")]
    #[Assert\NotBlank(message:"veuillez remplir le champs")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Groups("users")]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reclamation::class)]
    #[Groups("utilisateur")]
    private Collection $Reclamations;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Commande::class)]
    #[Groups("utilisateur")]
    private Collection $commandes;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("users")]
    private ?string $reset_token = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("users")]
    private ?string $authCode = null;

    #[ORM\Column(nullable: false)]
    private ?bool $isActive = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $disabledUntil = null;



    #[ORM\OneToOne(mappedBy: 'user',cascade:['persist','remove'], targetEntity: Panier::class)]
    #[Groups("user")]
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

    // public function __toString()
    // {
    //     return (string) $this->getPrenom();
    // }

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

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    public function setAuthCode(?string $authCode): self
    {
        $this->authCode = $authCode;

        return $this;
    }

     /**
     * Return true if the user should do two-factor authentication.
     */
    public function isEmailAuthEnabled(): bool
    {
 return true;
    }

    /**
     * Return user email address.
     */
    public function getEmailAuthRecipient(): string
    {
return $this->email;
    }

    /**
     * Return the authentication code.
     */
    public function getEmailAuthCode(): ?string
    {
 if(null == $this->authCode){
                         throw new \LogicalException('The email authentification code was not set');
                      }
 return $this->authCode;
    }

    /**
     * Set the authentication code.
     */
    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode =$authCode;

    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getDisabledUntil(): ?\DateTimeInterface
    {
        return $this->disabledUntil;
    }

    public function setDisabledUntil(?\DateTimeInterface $disabledUntil): self
    {
        $this->disabledUntil = $disabledUntil;

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
