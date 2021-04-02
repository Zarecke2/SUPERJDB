<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 */
class Document
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
    private $type_doc;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path_doc;

    /**
     * @ORM\Column(type="integer")
     */
    private $date_depot;


    /**
     * @ORM\ManyToOne(targetEntity=Etudiant::class, inversedBy="documentEtudiant")
     */
    private $etudiantDocument;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToOne(targetEntity=Post::class, mappedBy="DocumentPost", cascade={"persist", "remove"})
     */
    private $PostDocument;

    public function __toString()
    {
        return $this->getNom();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeDoc(): ?string
    {
        return $this->type_doc;
    }

    public function setTypeDoc(string $type_doc): self
    {
        $this->type_doc = $type_doc;

        return $this;
    }

    public function getPathDoc(): ?string
    {
        return $this->path_doc;
    }

    public function setPathDoc(string $path_doc): self
    {
        $this->path_doc = $path_doc;

        return $this;
    }

    public function getDateDepot(){
        return $this->date_depot;
    }

    public function setDateDepot(int $date_depot): self
    {
        $this->date_depot = $date_depot;

        return $this;
    }

    public function getEtudiantDocument(): ?Etudiant
    {
        return $this->etudiantDocument;
    }

    public function setEtudiantDocument(?Etudiant $etudiantDocument): self
    {
        $this->etudiantDocument = $etudiantDocument;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPostDocument(): ?Post
    {
        return $this->PostDocument;
    }

    public function setPostDocument(?Post $PostDocument): self
    {
        // unset the owning side of the relation if necessary
        if ($PostDocument === null && $this->PostDocument !== null) {
            $this->PostDocument->setDocumentPost(null);
        }

        // set the owning side of the relation if necessary
        if ($PostDocument !== null && $PostDocument->getDocumentPost() !== $this) {
            $PostDocument->setDocumentPost($this);
        }

        $this->PostDocument = $PostDocument;

        return $this;
    }
}
