<?php

namespace App\Entity;

use App\Repository\JournalDeBordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JournalDeBordRepository::class)
 */
class JournalDeBord
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
    private $nom_journal;

    /**
     * @ORM\Column(type="boolean")
     */
    private $lu;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="jdbPost",cascade="persist")
     */
    private $postJdb;

    /**
     * @ORM\OneToOne(targetEntity=Equipe::class, inversedBy="jdbEquipe", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $equipeJdb;

    public function __construct()
    {
        $this->postJdb = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->getNomJournal();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomJournal(): ?string
    {
        return $this->nom_journal;
    }

    public function setNomJournal(string $nom_journal): self
    {
        $this->nom_journal = $nom_journal;

        return $this;
    }

    public function getLu(): ?bool
    {
        return $this->lu;
    }

    public function setLu(bool $lu): self
    {
        $this->lu = $lu;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPostJdb(): Collection
    {
        return $this->postJdb;
    }

    public function addPostJdb(Post $postJdb): self
    {
        if (!$this->postJdb->contains($postJdb)) {
            $this->postJdb[] = $postJdb;
            $postJdb->setJdbPost($this);
        }

        return $this;
    }

    public function removePostJdb(Post $postJdb): self
    {
        if ($this->postJdb->removeElement($postJdb)) {
            // set the owning side to null (unless already changed)
            if ($postJdb->getJdbPost() === $this) {
                $postJdb->setJdbPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocumentJdb(): Collection
    {
        return $this->documentJdb;
    }

    public function addDocumentJdb(Document $documentJdb): self
    {
        if (!$this->documentJdb->contains($documentJdb)) {
            $this->documentJdb[] = $documentJdb;
            $documentJdb->setJdbDocument($this);
        }

        return $this;
    }

    public function removeDocumentJdb(Document $documentJdb): self
    {
        if ($this->documentJdb->removeElement($documentJdb)) {
            // set the owning side to null (unless already changed)
            if ($documentJdb->getJdbDocument() === $this) {
                $documentJdb->setJdbDocument(null);
            }
        }

        return $this;
    }

    public function getEquipeJdb(): ?Equipe
    {
        return $this->equipeJdb;
    }

    public function setEquipeJdb(Equipe $equipeJdb): self
    {
        $this->equipeJdb = $equipeJdb;

        return $this;
    }
}
