<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EtatMarche
 *
 * @ORM\Table(name="etat_marche", uniqueConstraints={@ORM\UniqueConstraint(name="code_etat_UNIQUE", columns={"code"})}, indexes={@ORM\Index(name="fk_etat_marche_etat_marche1_idx", columns={"etat_suivant"})})
 * @ORM\Entity
 */
class EtatMarche
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
     * @ORM\Column(name="code", type="string", length=45, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private $description;

    /**
     * @var EtatMarche
     *
     * @ORM\ManyToOne(targetEntity="EtatMarche")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_suivant", referencedColumnName="id")
     * })
     */
    private $etatSuivant;

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function setLibelle(string $liebelle): self
    {
        $this->libelle = $liebelle;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEtatSuivant()
    {
        return $this->etatSuivant;
    }

    public function setEtatSuivant($etatSuivant): self
    {
        $this->etatSuivant = $etatSuivant;

        return $this;
    }


}
