<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern:"/^[^0-9]+$/",
        message:"Le nom ne doit pas contenir de chiffres"
    )]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'genre', targetEntity: Reclamation::class)]
    private Collection $Reclamations;

    public function __construct()
    {
        $this->Reclamations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }
    
    public function __toString()
    {
        return (string) $this->getlibelle();
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->Reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->Reclamations->contains($reclamation)) {
            $this->Reclamations->add($reclamation);
            $reclamation->setGenre($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->Reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getGenre() === $this) {
                $reclamation->setGenre(null);
            }
        }

        return $this;
    }
}
