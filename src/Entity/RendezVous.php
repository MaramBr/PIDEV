<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
   #[Assert\GreaterThan('today')]
   private ?\DateTimeInterface $daterdv = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    private ?Coaching $Coachings = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDaterdv(): ?\DateTimeInterface
    {
        return $this->daterdv;
    }

    public function setDaterdv(\DateTimeInterface $daterdv): self
    {
        $this->daterdv = $daterdv;

        return $this;
    }

    public function getCoachings(): ?Coaching
    {
        return $this->Coachings;
    }

    public function setCoachings(?Coaching $Coachings): self
    {
        $this->Coachings = $Coachings;

        return $this;
    }
}
