<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
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
    private $nom_post;

    /**
     * @ORM\Column(type="boolean")
     */
    private $commentaire;

    /**
     * @ORM\Column(type="integer")
     */
    private $date_post;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\JournalDeBord", inversedBy="postJdb", cascade="persist")
     */
    private $jdbPost;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $related_to;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $texte;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, inversedBy="PostDocument", cascade={"persist", "remove"})
     */
    private $DocumentPost;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Posteur;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $lu;

    public function __toString()
    {
        return $this->getNomPost();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPost(): ?string
    {
        return $this->nom_post;
    }

    public function setNomPost(string $nom_post): self
    {
        $this->nom_post = $nom_post;

        return $this;
    }

    public function getCommentaire(): ?bool
    {
        return $this->commentaire;
    }

    public function setCommentaire(bool $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getDatePost()
    {
        return $this->date_post;
    }

    public function setDatePost(int $date_post): self
    {
        $this->date_post = $date_post;

        return $this;
    }

    public function getJdbPost(): ?JournalDeBord
    {
        return $this->jdbPost;
    }

    public function setJdbPost(?JournalDeBord $jdbPost): self
    {
        $this->jdbPost = $jdbPost;

        return $this;
    }

    public function getRelatedTo(): ?int
    {
        return $this->related_to;
    }

    public function setRelatedTo(?int $related_to): self
    {
        $this->related_to = $related_to;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getDocumentPost(): ?Document
    {
        return $this->DocumentPost;
    }

    public function setDocumentPost(?Document $DocumentPost): self
    {
        $this->DocumentPost = $DocumentPost;

        return $this;
    }

    public function getPosteur(): ?string
    {
        return $this->Posteur;
    }

    public function setPosteur(string $Posteur): self
    {
        $this->Posteur = $Posteur;

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
}
