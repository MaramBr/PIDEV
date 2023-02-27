<?php

namespace App\Entity;

use App\Repository\CoachingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoachingRepository::class)]
class Coaching
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Coaching")]
    private ?int $id = null;


   


    

    #[ORM\Column(length: 255)]
    #[Groups("Coaching")]
    #[Assert\NotBlank(message:"Cette valeur ne doit pas être vide.")] 
    private ?string $cours = null;

    #[ORM\Column(length: 255)]
    #[Groups("Coaching")]
    #[Assert\NotBlank(message:"Cette valeur ne doit pas être vide.")]
    private ?string $dispoCoach = null;

    #[ORM\Column(length: 255)]
    #[Groups("Coaching")]
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
    public function setId(int $id): self
    {
        $this->id = $id;

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

    public function __toString()
    {
        return (string)  $this->getCours();
       
        return(string)  $this->getDispoCoach();
    }

   
}
