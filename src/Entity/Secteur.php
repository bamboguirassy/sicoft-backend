<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Secteur
 *
 * @ORM\Table(name="secteur", uniqueConstraints={@ORM\UniqueConstraint(name="code_secteur_UNIQUE", columns={"code"})})
 * @ORM\Entity
 */
class Secteur
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Fournisseur")
     * @ORM\JoinTable(name="fournisseur_secteur",
     *      joinColumns={@ORM\JoinColumn(name="secteur", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fournisseur", referencedColumnName="id")}
     * )
     */
    protected $fournisseurs;
    
    public function __construct() {
        $this->fournisseurs = new ArrayCollection();
    }

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

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }


    public function getFournisseurs()
    {
        return $this->fournisseurs;
    }


    public function setFournisseurs($fournisseurs): self
    {
        $this->fournisseurs = $fournisseurs;
    }

    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }


}
