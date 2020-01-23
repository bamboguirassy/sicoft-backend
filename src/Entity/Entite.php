<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entite
 *
 * @ORM\Table(name="entite", uniqueConstraints={@ORM\UniqueConstraint(name="codentite_UNIQUE", columns={"code"})}, indexes={@ORM\Index(name="fk_entite_entite1_idx", columns={"entite_parent"}), @ORM\Index(name="fk_entite_typentite1_idx", columns={"type_entite"})})
 * @ORM\Entity
 */
class Entite
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
     * @ORM\Column(name="entite", type="string", length=200, nullable=false)
     */
    private $entite;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=false)
     */
    private $code;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @var \Entite
     *
     * @ORM\ManyToOne(targetEntity="Entite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="entite_parent", referencedColumnName="id", nullable=true)
     * })
     */
    private $entiteParent;

    /**
     * @var \TypeEntite
     *
     * @ORM\ManyToOne(targetEntity="TypeEntite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_entite", referencedColumnName="id")
     * })
     */
    private $typeEntite;

    public function getId()
    {
        return $this->id;
    }

    public function getEntite()
    {
        return $this->entite;
    }

    public function setEntite(string $entite): self
    {
        $this->entite = $entite;

        return $this;
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

    public function getEtat()
    {
        return $this->etat;
    }

    public function setEtat($etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getEntiteParent()
    {
        return $this->entiteParent;
    }

    public function setEntiteParent($entiteParent): self
    {
        $this->entiteParent = $entiteParent;

        return $this;
    }

    public function getTypeEntite()
    {
        return $this->typeEntite;
    }

    public function setTypeEntite($typeEntite): self
    {
        $this->typeEntite = $typeEntite;

        return $this;
    }


}
