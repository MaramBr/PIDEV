<?php

namespace App\Entity;

use App\Repository\CoachingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoachingRepository::class)]
class Coaching
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomCoach = null;

    #[ORM\Column(length: 255)]
    private ?string $prenomCoach = null;

    #[ORM\Column(length: 255)]
    private ?string $emailCoach = null;

    

    #[ORM\Column(length: 255)]
    private ?string $cours = null;

    #[ORM\Column(length: 255)]
    private ?string $dispoCoach = null;

    #[ORM\Column(length: 255)]
    private ?string $imgCoach = null;

    #[ORM\OneToMany(mappedBy: 'Coachings', targetEntity: RendezVous::class)]
    private Collection $rendezVouses;

    public function __construct()
    {
        $this->rendezVouses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCoach(): ?string
    {
        return $this->nomCoach;
    }

    public function setNomCoach(string $nomCoach): self
    {
        $this->nomCoach = $nomCoach;

        return $this;
    }

    public function getPrenomCoach(): ?string
    {
        return $this->prenomCoach;
    }

    public function setPrenomCoach(string $prenomCoach): self
    {
        $this->prenomCoach = $prenomCoach;

        return $this;
    }

    public function getEmailCoach(): ?string
    {
        return $this->emailCoach;
    }

    public function setEmailCoach(string $emailCoach): self
    {
        $this->emailCoach = $emailCoach;

        return $this;
    }

   

    public function getCours(): ?string
    {
        return $this->cours;
    }

    public function setCours(string $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    public function getDispoCoach(): ?string
    {
        return $this->dispoCoach;
    }

    public function setDispoCoach(string $dispoCoach): self
    {
        $this->dispoCoach = $dispoCoach;

        return $this;
    }

    public function getImgCoach(): ?string
    {
        return $this->imgCoach;
    }

    public function setImgCoach(string $imgCoach): self
    {
        $this->imgCoach = $imgCoach;

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVouses(): Collection
    {
        return $this->rendezVouses;
    }

    public function addRendezVouse(RendezVous $rendezVouse): self
    {
        if (!$this->rendezVouses->contains($rendezVouse)) {
            $this->rendezVouses->add($rendezVouse);
            $rendezVouse->setCoachings($this);
        }

        return $this;
    }

    public function removeRendezVouse(RendezVous $rendezVouse): self
    {
        if ($this->rendezVouses->removeElement($rendezVouse)) {
            // set the owning side to null (unless already changed)
            if ($rendezVouse->getCoachings() === $this) {
                $rendezVouse->setCoachings(null);
            }
        }

        return $this;
    }
}
