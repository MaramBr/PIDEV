<?php

namespace App\Entity;

use App\Repository\NotifyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotifyRepository::class)]
class Notify
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'notifies')]
    private ?RendezVous $rendezvous = null;

    #[ORM\ManyToOne(inversedBy: 'notifies')]
    private ?Coaching $coach = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getRendezvous(): ?RendezVous
    {
        return $this->rendezvous;
    }

    public function setRendezvous(?RendezVous $rendezvous): self
    {
        $this->rendezvous = $rendezvous;

        return $this;
    }


    public function __toString()
    {
        return (string)  $this->getMessage();
       
    }

    public function getCoach(): ?Coaching
    {
        return $this->coach;
    }

    public function setCoach(?Coaching $coach): self
    {
        $this->coach = $coach;

        return $this;
    }
}
