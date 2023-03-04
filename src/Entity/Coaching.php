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

    #[ORM\Column]
    private ?int $DislikeButton = 0;

    #[ORM\Column]
    private ?int $LikeButton = 0;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descCoach = null;

    #[ORM\OneToMany(mappedBy: 'coach', targetEntity: Notify::class)]
    private Collection $notifies;

   

   

    public function __construct()
    {
        $this->rendezVouses = new ArrayCollection();
        $this->notifies = new ArrayCollection();
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

    public function getDislikeButton(): ?int
    {
        return $this->DislikeButton;
    }

    public function setDislikeButton(int $DislikeButton): self
    {
        $this->DislikeButton = $DislikeButton;

        return $this;
    }

    public function getLikeButton(): ?int
    {
        return $this->LikeButton;
    }

    public function setLikeButton(int $LikeButton): self
    {
        $this->LikeButton = $LikeButton;

        return $this;
    }

    public function getDescCoach(): ?string
    {
        return $this->descCoach;
    }

    public function setDescCoach(string $descCoach): self
    {
        $this->descCoach = $descCoach;

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
            $notify->setCoach($this);
        }

        return $this;
    }

    public function removeNotify(Notify $notify): self
    {
        if ($this->notifies->removeElement($notify)) {
            // set the owning side to null (unless already changed)
            if ($notify->getCoach() === $this) {
                $notify->setCoach(null);
            }
        }

        return $this;
    }

    

   
}
