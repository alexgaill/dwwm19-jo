<?php

namespace App\Entity;

use App\Entity\Athlete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DisciplineRepository;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: DisciplineRepository::class)]
class Discipline
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private /* readonly */ int $id;

    #[ORM\Column(type:'string', length:40, unique:true)]
    private ?string $nom;

    #[ORM\OneToMany(mappedBy:'discipline', targetEntity:Athlete::class, orphanRemoval:true)]
    private $athletes;

    public function __construct()
    {
        $this->athletes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of nom
     *
     * @return string
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     *
     * @param string $nom
     *
     * @return self
     */
    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of athletes
     */
    public function getAthletes()
    {
        return $this->athletes;
    }
}
