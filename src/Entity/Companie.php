<?php

namespace App\Entity;

use App\Repository\CompanieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanieRepository::class)]
class Companie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'companies', targetEntity: Transporteur::class)]
    private Collection $transporteurs;

    public function __construct()
    {
        $this->transporteurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    
    public function __toString()
    {
        return (string) $this->getLibelle();
    }

    /**
     * @return Collection<int, Transporteur>
     */
    public function getTransporteurs(): Collection
    {
        return $this->transporteurs;
    }

    public function addTransporteur(Transporteur $transporteur): self
    {
        if (!$this->transporteurs->contains($transporteur)) {
            $this->transporteurs->add($transporteur);
            $transporteur->setCompanies($this);
        }

        return $this;
    }

    public function removeTransporteur(Transporteur $transporteur): self
    {
        if ($this->transporteurs->removeElement($transporteur)) {
            // set the owning side to null (unless already changed)
            if ($transporteur->getCompanies() === $this) {
                $transporteur->setCompanies(null);
            }
        }

        return $this;
    }
}
