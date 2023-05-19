<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 * @UniqueEntity(fields={"email"}, message="Cet email est déjà utilisé par un autre participant.")
 * @UniqueEntity(fields={"tel"}, message="Ce numero est déjà utilisé par un autre participant.")
 */

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Participant")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"nom evenement doit etre non vide")]
    #[Assert\Regex(
         pattern:"/^[^0-9]+$/",
         message:"Le nom ne doit pas contenir de chiffres"
     )]

     #[Groups("Participant")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"nom evenement doit etre non vide")]
    #[Assert\Regex(
         pattern:"/^[^0-9]+$/",
         message:"Le nom ne doit pas contenir de chiffres"
     )]
     #[Groups("Participant")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
     #[Assert\NotBlank(message:"l'email doit etre non vide")]
     #[Assert\Email(message:"L'email n'est pas valide ")]
     #[Groups("Participant")]
    private ?string $email = null;

    #[ORM\Column]
 #[Assert\NotBlank(message:"AGE doit etre non vide")]
 
#[Groups("Participant")]
    private ?int $age = null;

 
 #[ORM\Column]
   
    #[Assert\NotBlank(message : "Le numéro de téléphone est obligatoire.")]
    #[Assert\Length(
        max : 11,
        maxMessage : "Le numéro de téléphone ne doit pas contenir plus de {{ limit }} chiffres."
    )]
    #[Groups("Participant")]
    private ?string $tel = null;


    #[ORM\ManyToMany(targetEntity: Evenement::class, inversedBy: 'participants')]
    #[Groups("Participant")]
    private Collection $evenement;

    
    public function __construct()
    {
        $this->evenements = new ArrayCollection();
        $this->evenementParticipants = new ArrayCollection();
        $this->evenement = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    
  public function __toString()
    {
        return (string) $this->getNom();
        
        $this->getPrenom();
        $this->getEmail();
        $this->getAge();
        $this->getTel();
      
        
    }
  
    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(int $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenement->contains($evenement)) {
            $this->evenement->add($evenement);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        $this->evenement->removeElement($evenement);

        return $this;
        
    }

    
}
