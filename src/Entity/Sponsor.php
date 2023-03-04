<?php

namespace App\Entity;

use App\Repository\SponsorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: SponsorRepository::class)]
class Sponsor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"nom Sponsor doit etre non vide")]
    #[Assert\Length(min:3, minMessage:"Votre nom inferieure a 3 caractères.")]
    #[Assert\Regex(
         pattern:"/^[^0-9]+$/",
         message:"Le nom ne doit pas contenir de chiffres"
     )]
     #[Groups("Sponsor")]
    private ?string $nomSponsor = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"l'email doit etre non vide")]
     #[Assert\Email(message:"L'email n'est pas valide ")]
     #[Groups("Sponsor")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Investissement doit etre non vide")]
    #[Groups("Sponsor")]
    private ?string $invest = null;

    #[ORM\OneToMany(mappedBy: 'Sponsors', targetEntity: Evenement::class)]
    private Collection $evenements;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSponsor(): ?string
    {
        return $this->nomSponsor;
    }

    public function setNomSponsor(string $nomSponsor): self
    {
        $this->nomSponsor = $nomSponsor;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getInvest(): ?string
    {
        return $this->invest;
    }

    public function setInvest(string $invest): self
    {
        $this->invest = $invest;

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
            $evenement->setSponsors($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getSponsors() === $this) {
                $evenement->setSponsors(null);
            }
        }

        return $this;
    }

     public function __toString()
    {
        return (string) $this->getnomSponsor();
        $this->getEmail();
        $this->getInvest();
        
    }
}
