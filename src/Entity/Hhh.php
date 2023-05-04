<?php

namespace App\Entity;

use App\Repository\HhhRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HhhRepository::class)]
class Hhh
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
