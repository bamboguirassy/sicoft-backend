<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Budget
 *
 * @ORM\Table(name="budget", indexes={@ORM\Index(name="fk_budget_entite1_idx", columns={"entite"}), @ORM\Index(name="fk_budget_exercice1_idx", columns={"exercice"})})
 * @ORM\Entity
 */
class Budget
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
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

    /**
     * @var bool
     *
     * @ORM\Column(name="verrouille", type="boolean", nullable=false)
     */
    private $verrouille;

    /**
     * @var \Entite
     *
     * @ORM\ManyToOne(targetEntity="Entite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="entite", referencedColumnName="id")
     * })
     */
    private $entite;

    /**
     * @var \Exercice
     *
     * @ORM\ManyToOne(targetEntity="Exercice")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="exercice", referencedColumnName="id")
     * })
     */
    private $exercice;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVerrouille(): ?bool
    {
        return $this->verrouille;
    }

    public function setVerrouille(bool $verrouille): self
    {
        $this->verrouille = $verrouille;

        return $this;
    }

    public function getEntite(): ?Entite
    {
        return $this->entite;
    }

    public function setEntite(?Entite $entite): self
    {
        $this->entite = $entite;

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): self
    {
        $this->exercice = $exercice;

        return $this;
    }


}
