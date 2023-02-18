<?php

namespace App\Entity;

use App\Repository\TransporteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: TransporteurRepository::class)]
class Transporteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $vehiculeDescription = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column]
    private ?float $prixStandard = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'Companys')]
    private ?Company $company = null;

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

    public function getVehiculeDescription(): ?string
    {
        return $this->vehiculeDescription;
    }

    public function setVehiculeDescription(string $vehiculeDescription): self
    {
        $this->vehiculeDescription = $vehiculeDescription;

        return $this;
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

    public function getPrixStandard(): ?float
    {
        return $this->prixStandard;
    }

    public function setPrixStandard(float $prixStandard): self
    {
        $this->prixStandard = $prixStandard;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getNom();
        $this->getVehiculeDescription();
        $this->getNumero();
        $this->getPrixStandard();
        $this->getImage();
        $this->getCompany();
    }
}
