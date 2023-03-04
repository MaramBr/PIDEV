<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerilizerInterface;


#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

   #[ORM\Column(length: 255)]
   #[Groups("Evenement")]
   #[Assert\NotBlank(message:"nom evenement doit etre non vide")]
   #[Assert\Length(min:5, minMessage:"Votre nom inferieure a 5 caractères.")]
   #[Assert\Regex(
         pattern:"/^[^0-9]+$/",
         message:"Le nom ne doit pas contenir de chiffres"
     )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups("Evenement")]
    #[Assert\NotBlank(message:"lieu evenement doit etre non vide")]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    #[Groups("Evenement")]
    #[Assert\NotBlank(message:"type evenement doit etre non vide")]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Groups("Evenement")]
    #[Assert\NotBlank(message:"Description evenement doit etre non vide")]
     #[Assert\Length(min:7,max:100, minMessage:"Doit etre > 7.", maxMessage:"Doit etre <=100")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("Evenement")]

   #[Assert\GreaterThan('today')]
   #[Assert\LessThan('+2 year')]
 
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("Evenement")]
   
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255)]
    #[Groups("Evenement")]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'evenements')]
    private ?Sponsor $Sponsors = null;

    #[ORM\ManyToMany(cascade:['persist','remove'],targetEntity: Participant::class, mappedBy: 'evenement')]
    
    private Collection $participants;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Nombre Participant  doit etre non vide")]
    #[Assert\Length(min:0,max:1000, minMessage:"Doit etre > =0.", maxMessage:"Doit etre <=1000")]

     
    private ?string $nbParticipant = null;
    

    
    public function __construct()
    {
        $this->Participants = new ArrayCollection();
        $this->evenementParticipants = new ArrayCollection();
        $this->participants = new ArrayCollection();
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

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getSponsors(): ?Sponsor
    {
        return $this->Sponsors;
    }

    public function setSponsors(?Sponsor $Sponsors): self
    {
        $this->Sponsors = $Sponsors;

        return $this;
    }

     public function __toString()
    {
        return (string) $this->getNom();
        
        $this->getDateDebut();
        $this->getDateFin();
        $this->getImage();
        $this->getType();
        $this->getDescription();
        $this->getLieu();
        $this->getNbParticipant();
        $this->getSponsors();
      
       
        
    }

     /**
      * @return Collection<int, Participant>
      */
     public function getParticipants(): Collection
     {
         return $this->participants;
     }

     public function addParticipant(Participant $participant): self
     {
         if (!$this->participants->contains($participant)) {
             $this->participants->add($participant);
             $participant->addEvenement($this);
         }

         return $this;
     }

     public function removeParticipant(Participant $participant): self
     {
         if ($this->participants->removeElement($participant)) {
             $participant->removeEvenement($this);
         }

         return $this;
     }

     public function getNbParticipant(): ?string
     {
         return $this->nbParticipant;
     }

     public function setNbParticipant(string $nbParticipant): self
     {
         $this->nbParticipant = $nbParticipant;

         return $this;
     }

     
}
