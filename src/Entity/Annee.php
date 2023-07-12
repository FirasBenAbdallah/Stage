<?php

namespace App\Entity;

use App\Repository\AnneeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnneeRepository::class)]
class Annee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $anneescolaire = null;

    #[ORM\OneToMany(targetEntity: Projet::class, mappedBy: 'annee')] 
    private $projets;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnneeScolaire(): ?string
    {
        return $this->anneescolaire;
    }

    public function setAnneeScolaire(string $anneescolaire): static
    {
        $this->anneescolaire = $anneescolaire;

        return $this;
    }

    public function __toString()
    {
        return $this->anneescolaire;
    }
}
