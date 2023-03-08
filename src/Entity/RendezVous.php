<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
   #[Assert\GreaterThan('today',message:"La date saisie doit etre superier de la date d'aujourd'hui")]
   #[Assert\LessThanOrEqual('now +3 months',message:"La date saisie ne doit pas dépasser 3 mois à partir de la date actuelle")]
   private ?\DateTimeInterface $daterdv = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    private ?Coaching $Coachings = null;

    #[ORM\OneToMany(mappedBy: 'rendezvous', targetEntity: Notify::class)]
    private Collection $notifies;

    #[ORM\Column]
    private ?bool $etatrdv = false;

    public function __construct()
    {
        $this->notifies = new ArrayCollection();
    }
    

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

    /**
     * @return Collection<int, Notify>
     */
    public function getNotifies(): Collection
    {
        return $this->notifies;
    }

    public function addNotify(Notify $notify): self
    {
        if (!$this->notifies->contains($notify)) {
            $this->notifies->add($notify);
            $notify->setRendezvous($this);
        }

        return $this;
    }

    public function removeNotify(Notify $notify): self
    {
        if ($this->notifies->removeElement($notify)) {
            // set the owning side to null (unless already changed)
            if ($notify->getRendezvous() === $this) {
                $notify->setRendezvous(null);
            }
        }

        return $this;
    }

    public function isEtatrdv(): ?bool
    {
        return $this->etatrdv;
    }

    public function setEtatrdv(bool $etatrdv): self
    {
        $this->etatrdv = $etatrdv;

        return $this;
    }
}
