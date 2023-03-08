<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("commande")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("commande")]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255)]
    #[Groups("commande")]
    private ?string $status = 'En attente';

    #[ORM\Column]
    #[Groups("commande")]
    private ?float $montant = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups("commande")]
    private ?string $reference = null;

    #[ORM\OneToMany(mappedBy: 'Commande',cascade:['persist','remove'], targetEntity: CommandeProduit::class)]
    #[Groups("commande")]
    private Collection $commandeProduits;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups("commande","user")]
    private ?User $user = null;

    public function __construct()
    {
        $this->date_creation = date_create();
        $this->commandeProduits = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }
    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }


    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }


    public function getMontant(): ?float
    {
        return $this->montant;
    }
    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }


    public function getReference(): ?string
    {
        return $this->reference;
    }
    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }


    
    /**
     * @return Collection<int, CommandeProduit>
     */
    public function getCommandeProduits(): Collection
    {
        return $this->commandeProduits;
    }

    public function addCommandeProduit(CommandeProduit $commandeProduit): self
    {
        if (!$this->commandeProduits->contains($commandeProduit)) {
            $this->commandeProduits->add($commandeProduit);
            $commandeProduit->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeProduit(CommandeProduit $commandeProduit): self
    {
        if ($this->commandeProduits->removeElement($commandeProduit)) {
            // set the owning side to null (unless already changed)
            if ($commandeProduit->getCommande() === $this) {
                $commandeProduit->setCommande(null);
            }
        }

        return $this;
    }

    public function getuser(): ?User
    {
        return $this->user;
    }

    public function setuser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
