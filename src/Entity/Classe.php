<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classe
 *
 * @ORM\Table(name="classe", uniqueConstraints={@ORM\UniqueConstraint(name="categorie_classe_2", columns={"categorie_classe", "type_classe"}), @ORM\UniqueConstraint(name="numero_UNIQUE", columns={"numero"})}, indexes={@ORM\Index(name="categorie_classe", columns={"categorie_classe"}), @ORM\Index(name="type_classe", columns={"type_classe"})})
 * @ORM\Entity
 */
class Classe
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
     * @var \CategorieClasse
     *
     * @ORM\ManyToOne(targetEntity="CategorieClasse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie_classe", referencedColumnName="id")
     * })
     */
    private $categorieClasse;

    /**
     * @var \TypeClasse
     *
     * @ORM\ManyToOne(targetEntity="TypeClasse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_classe", referencedColumnName="id")
     * })
     */
    private $typeClasse;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SousClasse", mappedBy="classe")
     */
    private $sousClasses;

    public function __construct()
    {
        $this->sousClasses = new ArrayCollection();
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

    public function getCategorieClasse(): ?CategorieClasse
    {
        return $this->categorieClasse;
    }

    public function setCategorieClasse(?CategorieClasse $categorieClasse): self
    {
        $this->categorieClasse = $categorieClasse;

        return $this;
    }

    public function getTypeClasse(): ?TypeClasse
    {
        return $this->typeClasse;
    }

    public function setTypeClasse(?TypeClasse $typeClasse): self
    {
        $this->typeClasse = $typeClasse;

        return $this;
    }

    /**
     * @return Collection|SousClasse[]
     */
    public function getSousClasses(): Collection
    {
        return $this->sousClasses;
    }

    public function addSousClass(SousClasse $sousClass): self
    {
        if (!$this->sousClasses->contains($sousClass)) {
            $this->sousClasses[] = $sousClass;
            $sousClass->setClasse($this);
        }

        return $this;
    }

    public function removeSousClass(SousClasse $sousClass): self
    {
        if ($this->sousClasses->contains($sousClass)) {
            $this->sousClasses->removeElement($sousClass);
            // set the owning side to null (unless already changed)
            if ($sousClass->getClasse() === $this) {
                $sousClass->setClasse(null);
            }
        }

        return $this;
    }


}
