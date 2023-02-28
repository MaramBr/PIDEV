<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateR = null;

    #[ORM\Column(length: 255)]
    private ?string $TitreR = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $DescriptionR = null;

    #[ORM\Column(length: 255)]
    private ?string $StatusR = null;

    #[ORM\ManyToOne(inversedBy: 'Reclamations')]
    private ?Genre $genre = null;

    #[ORM\ManyToOne(inversedBy: 'Reclamations')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateR(): ?\DateTimeInterface
    {
        return $this->DateR;
    }

    public function setDateR(\DateTimeInterface $DateR): self
    {
        $this->DateR = $DateR;

        return $this;
    }

    public function getTitreR(): ?string
    {
        return $this->TitreR;
    }

    public function setTitreR(string $TitreR): self
    {
        $this->TitreR = $TitreR;

        return $this;
    }

    public function getDescriptionR(): ?string
    {
        return $this->DescriptionR;
    }

    public function setDescriptionR(string $DescriptionR): self
    {
        $this->DescriptionR = $DescriptionR;

        return $this;
    }

    public function getStatusR(): ?string
    {
        return $this->StatusR;
    }

    public function setStatusR(string $StatusR): self
    {
        $this->StatusR = $StatusR;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
