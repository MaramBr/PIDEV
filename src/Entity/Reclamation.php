<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Reclamations")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("Reclamations")]
    private ?\DateTimeInterface $DateR = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"attention!!vide!!")]
    #[Groups("Reclamations")]
    private ?string $TitreR = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:" le champ est vide")]
    #[Assert\Length(min:5,maxMessage:"Votre numero est invalide")]
    #[Groups("Reclamations")]
    private ?string $DescriptionR = null;

    #[ORM\Column(length: 255)]
    #[Groups("Reclamations")]
    private ?string $StatusR = 'En attente';

    #[ORM\ManyToOne(inversedBy: 'Reclamations')]
    #[Assert\NotBlank(message:"attention!!vide!")]
    private ?Genre $genre = null;

    #[ORM\ManyToOne(inversedBy: 'Reclamations')]
    #[Assert\NotBlank(message:"attention!!vide!")]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups("Reclamations")]
    private ?string $Traitement = 'Pas de traitement';

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
        $this->DescriptionR = $DescriptionR ;

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

    public function getTraitement(): ?string
    {
        return $this->Traitement;
    }

    public function setTraitement(string $Traitement): self
    {
        $this->Traitement = $Traitement;

        return $this;
    }
}
