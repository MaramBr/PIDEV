<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\PanierRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
  
    #[ORM\ManyToMany(mappedBy: 'panier', targetEntity: Produit::class)]
    #[ORM\JoinTable(name:'panierproduit')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $produits;

    #[ORM\OneToOne(inversedBy: 'panier',cascade:['persist','remove'], targetEntity: User::class)]
    private ?User $utilisateur = null;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->addPanier($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        $this->produits->removeElement($produit);
        $produit->removePanier($this);

        return $this;
    }
    public function getUtilisateur():User 
    {
        return $this->utilisateur;
    }
    public function setUtilisateur(?User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }


    /*
     * @return Collection<int, User>
    
    public function getUtilisateur():User 
    {
        return $this->utilisateur;
    }
    public function setUlisateur(?User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

   public function addUtilisateur(User $utilisateur): self
    {
        if (!$this->utilisateur->contains($utilisateur)) {
            $this->utilisateur->add($utilisateur);
            $utilisateur->setPanier($this);
        }

        return $this;
    }

    public function removeUtilisateur(User $utilisateur): self
    {
        if ($this->utilisateur->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getPanier() === $this) {
                $utilisateur->setPanier(null);
            }
        }

        return $this;
    }  */
}
