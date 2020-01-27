<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExerciceSourceFinancement
 *
 * @ORM\Table(name="exercice_source_financement", indexes={@ORM\Index(name="fk_exercice_source_financement_exercice1_idx", columns={"exercice_id"}), @ORM\Index(name="fk_exercice_source_financement_source_financement1_idx", columns={"source_financement_id"})})
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
     * @var \Exercice
     *
     * @ORM\ManyToOne(targetEntity="Exercice")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="exercice_id", referencedColumnName="id")
     * })
     */
    private $exercice;

    /**
     * @var \SourceFinancement
     *
     * @ORM\ManyToOne(targetEntity="SourceFinancement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_financement_id", referencedColumnName="id")
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

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): self
    {
        $this->exercice = $exercice;

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
