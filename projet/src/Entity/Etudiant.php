<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=EtudiantRepository::class)
 */
class Etudiant implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $num_etudiant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_etudiant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom_etudiant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipe", inversedBy="etudiantEquipe", cascade="persist")
     */
    private $equipeEtudiant;
    

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="etudiantDocument")
     */
    private $documentEtudiant;

    /**
     * @ORM\ManyToOne(targetEntity=Enseignant::class, inversedBy="etudiantEnseignant")
     */
    private $enseignantEtudiant;

    public function __construct()
    {
        $this->documentEtudiant = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getPrenomEtudiant() . " " . $this->getNomEtudiant();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumEtudiant(): ?int
    {
        return $this->num_etudiant;
    }
    
    public function getRoles()
    {
        return ["ROLE_ETUDIANT", "ROLE_USER"];
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

    public function setNumEtudiant(int $num_etudiant): self
    {
        $this->num_etudiant = $num_etudiant;

        return $this;
    }

    public function getNomEtudiant(): ?string
    {
        return $this->nom_etudiant;
    }

    public function setNomEtudiant(string $nom_etudiant): self
    {
        $this->nom_etudiant = $nom_etudiant;

        return $this;
    }

    public function getPrenomEtudiant(): ?string
    {
        return $this->prenom_etudiant;
    }

    public function setPrenomEtudiant(string $prenom_etudiant): self
    {
        $this->prenom_etudiant = $prenom_etudiant;

        return $this;
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

    public function getEquipeEtudiant(): ?Equipe
    {
        return $this->equipeEtudiant;
    }

    public function setEquipeEtudiant(?Equipe $equipeEtudiant): self
    {
        $this->equipeEtudiant = $equipeEtudiant;

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocumentEtudiant(): Collection
    {
        return $this->documentEtudiant;
    }

    public function addDocumentEtudiant(Document $documentEtudiant): self
    {
        if (!$this->documentEtudiant->contains($documentEtudiant)) {
            $this->documentEtudiant[] = $documentEtudiant;
            $documentEtudiant->setEtudiantDocument($this);
        }

        return $this;
    }

    public function removeDocumentEtudiant(Document $documentEtudiant): self
    {
        if ($this->documentEtudiant->removeElement($documentEtudiant)) {
            // set the owning side to null (unless already changed)
            if ($documentEtudiant->getEtudiantDocument() === $this) {
                $documentEtudiant->setEtudiantDocument(null);
            }
        }

        return $this;
    }

    public function getEnseignantEtudiant(): ?Enseignant
    {
        return $this->enseignantEtudiant;
    }

    public function setEnseignantEtudiant(?Enseignant $enseignantEtudiant): self
    {
        $this->enseignantEtudiant = $enseignantEtudiant;

        return $this;
    }
}
