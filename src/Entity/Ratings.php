<?php

namespace App\Entity;

use App\Repository\RatingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatingsRepository::class)]
class Ratings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_rating = null;

    #[ORM\Column(length: 255)]
    private ?string $userName = null;

    #[ORM\Column]
    private ?float $rating = null;

    #[ORM\ManyToOne(inversedBy: 'Produit')]
    private ?Produit $Produit = null;

    public function getId_Rating(): ?int
    {
        return $this->id_rating;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getProduits(): ?Produit
    {
        return $this->Produit;
    }

    public function setProduits(?Produit $Produit): self
    {
        $this->Produit = $Produit;

        return $this;
    }
}
