<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Transporteur::class)]
    private Collection $Companys;

    public function __construct()
    {
        $this->Companys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * @return Collection<int, Transporteur>
     */
    public function getCompanys(): Collection
    {
        return $this->Companys;
    }

    public function addCompany(Transporteur $company): self
    {
        if (!$this->Companys->contains($company)) {
            $this->Companys->add($company);
            $company->setCompany($this);
        }

        return $this;
    }

    public function removeCompany(Transporteur $company): self
    {
        if ($this->Companys->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getCompany() === $this) {
                $company->setCompany(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getCompanyName();
        
    }
}
