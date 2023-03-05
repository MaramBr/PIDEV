<?php

namespace App\Entity;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;



#[ORM\Entity(repositoryClass: ProduitRepository::class)]
/**
 * @ORM\Entity
 */




class Produit
{
     









    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Produit")]
    
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"veuillez remplir le champs")]
    #[Assert\Length(min:3, minMessage:"Votre nom inferieure a 3 caractères.")]
    #[Assert\Regex(
        pattern:"/^[^0-9]+$/",
        message:"Le nom ne doit pas contenir de chiffres"
    )]
    #[Groups("Produit")]
     
    
     
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message:"veuillez remplir le champs")]
    #[Assert\Length(min:7,max:100, minMessage:"Doit etre > 7.", maxMessage:"Doit etre <=100")]
    #[Groups("Produit")]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"veuillez remplir le champs")]
    #[Groups("Produit")]
    private ?int $quantite = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"veuillez remplir le champs")]
    #[Assert\PositiveOrZero(message:"Le prix doit être un nombre positif.")]
    #[Assert\Range(
          min : 0,
         max : 99999999.99,
         notInRangeMessage : "Le prix doit avoir deux décimales maximum."
    )]
    #[Groups("Produit")]
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    #[Groups("Produit")]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'Produit')]
    #[Groups("Produit")]
    private ?Category $Categorys = null;

    #[ORM\OneToMany(mappedBy: 'Produit', targetEntity: CommandeProduit::class)]
    #[Groups("Produit")]
    private Collection $Commande;

    #[ORM\OneToMany(mappedBy: 'produits', targetEntity: Comments::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    #[ORM\Column(nullable: true)]
    private ?int $dislikes = null;

   
    #[ORM\ManyToMany(inversedBy: 'produits',targetEntity: Panier::class)]
    #[ORM\JoinTable(name:'panierproduit')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups("produit")]
    private Collection $panier;

    #[ORM\ManyToMany(targetEntity: Favorie::class, mappedBy: 'produit')]
    private Collection $favories;

   
    #[ORM\ManyToMany(inversedBy: 'produits',targetEntity: Panier::class)]
    #[ORM\JoinTable(name:'panierproduit')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups("produit")]
    private Collection $panier;

    #[ORM\ManyToMany(targetEntity: Favorie::class, mappedBy: 'produit')]
    private Collection $favories;

    public function __construct()
    {
        $this->Commande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

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

    public function getCategorys(): ?Category
    {
        return $this->Categorys;
    }

    public function setCategorys(?Category $Categorys): self
    {
        $this->Categorys = $Categorys;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getNom();
        $this->getDescription();
        $this->getQuantite();
        $this->getPrix();
        $this->getImage();
        $this->getCategorys();
    }

    /**
     * @return Collection<int, CommandeProduit>
     */
    public function getCommande(): Collection
    {
        return $this->Commande;
    }
    public function __toString()
    {
        return (string) $this->getNom();
        $this->getDescription();
        $this->getQuantite();
        $this->getPrix();
        $this->getImage();
        $this->getCategorys();
    }

    public function addCommande(CommandeProduit $commande): self
    {
        if (!$this->Commande->contains($commande)) {
            $this->Commande->add($commande);
            $commande->setProduit($this);
        }

        return $this;
    }

    public function removeCommande(CommandeProduit $commande): self
    {
        if ($this->Commande->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getProduit() === $this) {
                $commande->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setProduits($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProduits() === $this) {
                $comment->setProduits(null);
            }
        }

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getDislikes(): ?int
    {
        return $this->dislikes;
    }

    public function setDislikes(?int $dislikes): self
    {
        $this->dislikes = $dislikes;

        return $this;
    }


    /**
     * @return Collection|Panier[]
     */
    public function getPanier(): Collection
    {
        return $this->panier;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->panier->contains($panier)) {
            $this->panier->add($panier);
            $panier->addProduit($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->panier->removeElement($panier)) {
            $panier->removeProduit($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorie>
     */
    public function getFavories(): Collection
    {
        return $this->favories;
    }

    public function addFavory(Favorie $favory): self
    {
        if (!$this->favories->contains($favory)) {
            $this->favories->add($favory);
            $favory->addProduit($this);
        }

        return $this;
    }

    public function removeFavory(Favorie $favory): self
    {
        if ($this->favories->removeElement($favory)) {
            $favory->removeProduit($this);
        }

        return $this;
    }

}
