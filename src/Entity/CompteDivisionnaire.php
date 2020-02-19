<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompteDivisionnaire
 *
 * @ORM\Table(name="compte_divisionnaire", indexes={@ORM\Index(name="fk_compte_divisionnaire_sous_classe1_idx", columns={"sous_classe"})})
 * @ORM\Entity
 */
class CompteDivisionnaire
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
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=100, nullable=true)
     */
    private $description;

    /**
     * @var \SousClasse
     *
     * @ORM\ManyToOne(targetEntity="SousClasse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sous_classe", referencedColumnName="id")
     * })
     */
    private $sousClasse;

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

    public function getSousClasse(): ?SousClasse
    {
        return $this->sousClasse;
    }

    public function setSousClasse(?SousClasse $sousClasse): self
    {
        $this->sousClasse = $sousClasse;

        return $this;
    }


}
