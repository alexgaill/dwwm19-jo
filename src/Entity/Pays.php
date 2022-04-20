<?php

namespace App\Entity;

use App\Repository\PaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: PaysRepository::class)]
class Pays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private /* readonly */ int $id;

    #[ORM\Column(type:'string', length:30, unique:true)]
    private ?string $nom;

    #[ORM\Column(type:'string', length:40)]
    private string|File|null $drapeau;

    #[ORM\OneToMany(mappedBy:'pays', targetEntity:Athlete::class, orphanRemoval:true)]
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
     * Get the value of drapeau
     *
     * @return string|File|null
     */
    public function getDrapeau(): string|File|null
    {
        return $this->drapeau;
    }

    /**
     * Set the value of drapeau
     *
     * @param string|File|null $drapeau
     *
     * @return self
     */
    public function setDrapeau(string|File|null $drapeau): self
    {
        $this->drapeau = $drapeau;

        return $this;
    }

    /**
     * Get the value of athletes
     */
    public function getAthletes(): ?ArrayCollection
    {
        return $this->athletes;
    }
}
