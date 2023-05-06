<?php

namespace App\Entity;

use App\Repository\TransporteurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransporteurRepository::class)]
class Transporteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Transporteur")]
    #[Assert\NotBlank(message:"is empty")]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"is empty")]
    #[Groups("Transporteur")]
    private ?int $numero = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"is empty")]
    #[Groups("Transporteur")]
    private ?int $prix = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"is empty")]
    #[Groups("Transporteur")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"is empty")]
    #[Groups("Transporteur")]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'transporteurs')]
    #[Groups("Transporteur")]
    #[Assert\NotBlank(message:"is empty")]
    private ?Companie $companies = null;

    #[ORM\Column(length: 255)]
    #[Groups("Transporteur")]
    #[Assert\NotBlank(message:"is empty")]
    private ?string $nom = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCompanies(): ?Companie
    {
        return $this->companies;
    }

    public function setCompanies(?Companie $companies): self
    {
        $this->companies = $companies;

        return $this;
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
}
