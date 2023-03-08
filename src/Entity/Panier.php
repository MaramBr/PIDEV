<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\PanierRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("panier")]
    private ?int $id = null;
  
    #[ORM\ManyToMany(mappedBy: 'panier', targetEntity: Produit::class)]
    #[ORM\JoinTable(name:'panierproduit')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups("panier")]
    private $produits;

    #[ORM\OneToOne(inversedBy: 'panier',cascade:['persist','remove'], targetEntity: User::class)]
    #[Groups("panier")]
    private ?User $user = null;

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
    public function getuser():User 
    {
        return $this->user;
    }
    public function setuser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


    /*
     * @return Collection<int, User>
    
    public function getuser():User 
    {
        return $this->user;
    }
    public function setUlisateur(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

   public function adduser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setPanier($this);
        }

        return $this;
    }

    public function removeuser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getPanier() === $this) {
                $user->setPanier(null);
            }
        }

        return $this;
    }  */
}
