<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SousClasse
 *
 * @ORM\Table(name="sous_classe", indexes={@ORM\Index(name="fk_sous_classe_classe1_idx", columns={"classe"})})
 * @ORM\Entity
 */
class SousClasse
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=45, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     */
    private $libelle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Classe", inversedBy="sousClasses")
     * @ORM\JoinColumn(nullable=false,name="classe")
     */
    private $classe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CompteDivisionnaire", mappedBy="sousClasse")
     */
    private $compteDivisionnaires;

    public function __construct()
    {
        $this->compteDivisionnaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * @return Collection|CompteDivisionnaire[]
     */
    public function getCompteDivisionnaire(): Collection
    {
        return $this->compteDivisionnaires;
    }

    public function addCompteDivisionnaire(CompteDivisionnaire $compteDivisionnaire): self
    {
        if (!$this->compteDivisionnaires->contains($compteDivisionnaire)) {
            $this->compteDivisionnaires[] = $compteDivisionnaire;
            $compteDivisionnaire->setSousClasse($this);
        }

        return $this;
    }

    public function removeCompteDivisionnaire(CompteDivisionnaire $compteDivisionnaire): self
    {
        if ($this->compteDivisionnaires->contains($compteDivisionnaire)) {
            $this->compteDivisionnaires->removeElement($compteDivisionnaire);
            // set the owning side to null (unless already changed)
            if ($compteDivisionnaire->getSousClasse() === $this) {
                $compteDivisionnaire->setSousClasse(null);
            }
        }

        return $this;
    }

    /*public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }*/


}
