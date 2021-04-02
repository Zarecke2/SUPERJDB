<?php

namespace App\Entity;

use App\Repository\EnseignantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Service\Administrateur;

/**
 * @ORM\Entity(repositoryClass=EnseignantRepository::class)
 */
class Enseignant implements UserInterface
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
    private $nom_enseignant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom_enseignant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\OneToMany(targetEntity=Equipe::class, mappedBy="enseignantEquipe")
     */
    private $EquipeEnseignant;

    /**
     * @ORM\OneToMany(targetEntity=Etudiant::class, mappedBy="enseignantEtudiant")
     */
    private $etudiantEnseignant;

    public function __construct()
    {
        $this->EquipeEnseignant = new ArrayCollection();
        $this->etudiantEnseignant = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getPrenomEnseignant().' '.$this->getNomEnseignant();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEnseignant(): ?string
    {
        return $this->nom_enseignant;
    }

    public function setNomEnseignant(string $nom_enseignant): self
    {
        $this->nom_enseignant = $nom_enseignant;

        return $this;
    }

    public function getPrenomEnseignant(): ?string
    {
        return $this->prenom_enseignant;
    }

    public function setPrenomEnseignant(string $prenom_enseignant): self
    {
        $this->prenom_enseignant = $prenom_enseignant;

        return $this;
    }

    public function getRoles()
    {        
        if ($this->getAdministrateur() == true)
        {
            return ["ROLE_ENSEIGNANT", "ROLE_ADMINISTRATEUR", "ROLE_USER"];
        }
        return ["ROLE_ENSEIGNANT", "ROLE_USER"];
    }

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
    }

    public function getUsername() {
        return $this->login;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    /**
     * @return Collection|Equipe[]
     */
    public function getEquipeEnseignant(): Collection
    {
        return $this->EquipeEnseignant;
    }

    public function addEquipeEnseignant(Equipe $equipeEnseignant): self
    {
        if (!$this->EquipeEnseignant->contains($equipeEnseignant)) {
            $this->EquipeEnseignant[] = $equipeEnseignant;
            $equipeEnseignant->setEnseignantEquipe($this);
        }

        return $this;
    }

    public function removeEquipeEnseignant(Equipe $equipeEnseignant): self
    {
        if ($this->EquipeEnseignant->removeElement($equipeEnseignant)) {
            // set the owning side to null (unless already changed)
            if ($equipeEnseignant->getEnseignantEquipe() === $this) {
                $equipeEnseignant->setEnseignantEquipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Etudiant[]
     */
    public function getEtudiantEnseignant(): Collection
    {
        return $this->etudiantEnseignant;
    }

    public function addEtudiantEnseignant(Etudiant $etudiantEnseignant): self
    {
        if (!$this->etudiantEnseignant->contains($etudiantEnseignant)) {
            $this->etudiantEnseignant[] = $etudiantEnseignant;
            $etudiantEnseignant->setEnseignantEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiantEnseignant(Etudiant $etudiantEnseignant): self
    {
        if ($this->etudiantEnseignant->removeElement($etudiantEnseignant)) {
            // set the owning side to null (unless already changed)
            if ($etudiantEnseignant->getEnseignantEtudiant() === $this) {
                $etudiantEnseignant->setEnseignantEtudiant(null);
            }
        }

        return $this;
    }
}
