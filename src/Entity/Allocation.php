<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Allocation
 *
 * @ORM\Table(name="allocation", indexes={@ORM\Index(name="exercice_source_financement", columns={"exercice_source_financement"}), @ORM\Index(name="fk_allocation_Compte1_idx", columns={"compte"})})
 * @ORM\Entity
 */
class Allocation
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
     * @ORM\Column(name="montant_initial", type="bigint", nullable=false)
     */
    private $montantInitial;

    /**
     * @var int|null
     *
     * @ORM\Column(name="credit_inscrit", type="bigint", nullable=true)
     */
    private $creditInscrit;

    /**
     * @var int|null
     *
     * @ORM\Column(name="engagement_anterieur", type="bigint", nullable=true)
     */
    private $engagementAnterieur;

    /**
     * @var int|null
     *
     * @ORM\Column(name="montant_restant", type="bigint", nullable=true)
     */
    private $montantRestant;

    /**
     * @var ExerciceSourceFinancement
     *
     * @ORM\ManyToOne(targetEntity="ExerciceSourceFinancement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="exercice_source_financement", referencedColumnName="id")
     * })
     */
    private $exerciceSourceFinancement;

    /**
     * @var Compte
     *
     * @ORM\ManyToOne(targetEntity="Compte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte", referencedColumnName="id")
     * })
     */
    private $compte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantInitial(): ?string
    {
        return $this->montantInitial;
    }

    public function setMontantInitial(string $montantInitial): self
    {
        $this->montantInitial = $montantInitial;

        return $this;
    }

    public function getCreditInscrit(): ?string
    {
        return $this->creditInscrit;
    }

    public function setCreditInscrit(?string $creditInscrit): self
    {
        $this->creditInscrit = $creditInscrit;

        return $this;
    }

    public function getEngagementAnterieur(): ?string
    {
        return $this->engagementAnterieur;
    }

    public function setEngagementAnterieur(?string $engagementAnterieur): self
    {
        $this->engagementAnterieur = $engagementAnterieur;

        return $this;
    }

    public function getMontantRestant(): ?string
    {
        return $this->montantRestant;
    }

    public function setMontantRestant(?string $montantRestant): self
    {
        $this->montantRestant = $montantRestant;

        return $this;
    }

    public function getExerciceSourceFinancement(): ?ExerciceSourceFinancement
    {
        return $this->exerciceSourceFinancement;
    }

    public function setExerciceSourceFinancement(?ExerciceSourceFinancement $exerciceSourceFinancement): self
    {
        $this->exerciceSourceFinancement = $exerciceSourceFinancement;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }


}
