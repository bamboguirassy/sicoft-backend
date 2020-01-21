<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compte
 *
 * @ORM\Table(name="compte", uniqueConstraints={@ORM\UniqueConstraint(name="numero_compte_UNIQUE", columns={"numero"})}, indexes={@ORM\Index(name="fk_Compte_compte_divisionnaire1_idx", columns={"compte_divisionnaire"})})
 * @ORM\Entity
 */
class Compte
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
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private $description;

    /**
     * @var \CompteDivisionnaire
     *
     * @ORM\ManyToOne(targetEntity="CompteDivisionnaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_divisionnaire", referencedColumnName="id")
     * })
     */
    private $compteDivisionnaire;

    public function getId()
    {
        return $this->id;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCompteDivisionnaire()
    {
        return $this->compteDivisionnaire;
    }

    public function setCompteDivisionnaire($compteDivisionnaire): self
    {
        $this->compteDivisionnaire = $compteDivisionnaire;

        return $this;
    }


}
