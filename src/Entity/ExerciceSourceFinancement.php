<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExerciceSourceFinancement
 *
 * @ORM\Table(name="exercice_source_financement", indexes={@ORM\Index(name="budget", columns={"budget"}), @ORM\Index(name="fk_exercice_source_financement_source_financement1_idx", columns={"source_financement"})})
 * @ORM\Entity
 */
class ExerciceSourceFinancement
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
     * @var int
     *
     * @ORM\Column(name="montant", type="bigint", nullable=false)
     */
    private $montant;

    /**
     * @var \Budget
     *
     * @ORM\ManyToOne(targetEntity="Budget")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="budget", referencedColumnName="id")
     * })
     */
    private $budget;

    /**
     * @var \SourceFinancement
     *
     * @ORM\ManyToOne(targetEntity="SourceFinancement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_financement", referencedColumnName="id")
     * })
     */
    private $sourceFinancement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getBudget(): ?Budget
    {
        return $this->budget;
    }

    public function setBudget(?Budget $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getSourceFinancement(): ?SourceFinancement
    {
        return $this->sourceFinancement;
    }

    public function setSourceFinancement(?SourceFinancement $sourceFinancement): self
    {
        $this->sourceFinancement = $sourceFinancement;

        return $this;
    }


}
