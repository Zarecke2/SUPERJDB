<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EquipeRepository::class)
 */
class Equipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_equipe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Etudiant", mappedBy="equipeEtudiant",cascade="persist")
     */
    private $etudiantEquipe;

    /**
     * @ORM\OneToOne(targetEntity=JournalDeBord::class, mappedBy="equipeJdb", cascade={"persist", "remove"})
     */
    private $jdbEquipe;

    /**
     * @ORM\ManyToOne(targetEntity=Enseignant::class, inversedBy="EquipeEnseignant")
     */
    private $enseignantEquipe;

    public function __construct()
    {
        $this->etudiantEquipe = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNomEquipe();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEquipe(): ?string
    {
        return $this->nom_equipe;
    }

    public function setNomEquipe(string $nom_equipe): self
    {
        $this->nom_equipe = $nom_equipe;

        return $this;
    }

    /**
     * @return Collection|Etudiant[]
     */
    public function getEtudiantEquipe(): Collection
    {
        return $this->etudiantEquipe;
    }

    public function addEtudiantEquipe(Etudiant $etudiantEquipe): self
    {
        if (!$this->etudiantEquipe->contains($etudiantEquipe)) {
            $this->etudiantEquipe[] = $etudiantEquipe;
            $etudiantEquipe->setEquipeEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiantEquipe(Etudiant $etudiantEquipe): self
    {
        if ($this->etudiantEquipe->removeElement($etudiantEquipe)) {
            // set the owning side to null (unless already changed)
            if ($etudiantEquipe->getEquipeEtudiant() === $this) {
                $etudiantEquipe->setEquipeEtudiant(null);
            }
        }

        return $this;
    }

    public function getJdbEquipe(): ?JournalDeBord
    {
        return $this->jdbEquipe;
    }

    public function setJdbEquipe(JournalDeBord $jdbEquipe): self
    {
        // set the owning side of the relation if necessary
        if ($jdbEquipe->getEquipeJdb() !== $this) {
            $jdbEquipe->setEquipeJdb($this);
        }

        $this->jdbEquipe = $jdbEquipe;

        return $this;
    }

    public function getEnseignantEquipe(): ?Enseignant
    {
        return $this->enseignantEquipe;
    }

    public function setEnseignantEquipe(?Enseignant $enseignantEquipe): self
    {
        $this->enseignantEquipe = $enseignantEquipe;

        return $this;
    }
}
