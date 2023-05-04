<?php

namespace App\Entity;

use App\Repository\ProduitLikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitLikeRepository::class)]
class ProduitLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'produitLikes')]
    private ?Produit $produit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduitId(): ?Produit
    {
        return $this->produit;
    }

    public function setProduitId(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
}
