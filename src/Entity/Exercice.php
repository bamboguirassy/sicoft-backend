<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Exercice
 *
 * @ORM\Table(name="exercice")
 * @ORM\Entity
 */
class Exercice
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
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="datetime", nullable=false)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="datetime", nullable=false)
     */
    private $dateFin;
    
    /**
     * @var bool|null
     *
     * @ORM\Column(name="encours", type="boolean", nullable=true)
     */
    private $encours;

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode(string $code)
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    public function setDateDebut($dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin()
    {
        return $this->dateFin;
    }

    public function setDateFin($dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getEncours()
    {
        return $this->encours;
    }

    public function setEncours($encours): self
    {
        $this->encours = $encours;

        return $this;
    }


}
