<?php

namespace App\Entity;

use App\Repository\AthleteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: AthleteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Athlete
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private /* readonly */ int $id;

    #[ORM\Column(type:'string', length:65)]
    private string $nom;

    #[ORM\Column(type:'string', length:65)]
    private string $prenom;

    #[ORM\Column(type:'date')]
    private \DateTime $dateNaissance;

    #[ORM\Column(type:'string', length:40)]
    private string|File $photo;

    #[ORM\ManyToOne(targetEntity: Pays::class, inversedBy: 'athletes')]
    #[ORM\JoinColumn(nullable: false)]
    private Pays $pays;

    #[ORM\ManyToOne(targetEntity: Discipline::class, inversedBy: 'athletes')]
    #[ORM\JoinColumn(nullable: false)]
    private Discipline $discipline;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of nom
     *
     * @return string
     */
    public function getNom(): string
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
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of prenom
     *
     * @return string
     */
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    /**
     * Set the value of prenom
     *
     * @param string $prenom
     *
     * @return self
     */
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get the value of dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance(): \DateTime
    {
        return $this->dateNaissance;
    }

    /**
     * Set the value of dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return self
     */
    public function setDateNaissance(\DateTime $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get the value of photo
     *
     * @return string|File
     */
    public function getPhoto(): string|File
    {
        return $this->photo;
    }

    /**
     * Set the value of photo
     *
     * @param string|File $photo
     *
     * @return self
     */
    public function setPhoto(string|File $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get the value of pays
     *
     * @return Pays
     */
    public function getPays(): Pays
    {
        return $this->pays;
    }

    /**
     * Set the value of pays
     *
     * @param Pays $pays
     *
     * @return self
     */
    public function setPays(Pays $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get the value of discipline
     *
     * @return Discipline
     */
    public function getDiscipline(): Discipline
    {
        return $this->discipline;
    }

    /**
     * Set the value of discipline
     *
     * @param Discipline $discipline
     *
     * @return self
     */
    public function setDiscipline(Discipline $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    #[ORM\PostRemove]
    public function deletePhoto(): void
    {
        if (file_exists(__DIR__.'/../../public/img/upload/profil/'. $this->photo)) {
            unlink(__DIR__.'/../../public/img/upload/profil/'. $this->photo);
        }
    }
}
