<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SourceFinancement
 *
 * @ORM\Table(name="source_financement", indexes={@ORM\Index(name="fk_source_de_financement_type_source_de_financement1_idx", columns={"type"})})
 * @ORM\Entity
 */
class SourceFinancement
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
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var \TypeSourceFinancement
     *
     * @ORM\ManyToOne(targetEntity="TypeSourceFinancement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type", referencedColumnName="id")
     * })
     */
    private $type;

    public function getId()
    {
        return $this->id;
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

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }


}
