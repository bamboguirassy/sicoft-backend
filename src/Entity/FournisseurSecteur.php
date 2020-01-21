<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FournisseurSecteur
 *
 * @ORM\Table(name="fournisseur_secteur", uniqueConstraints={@ORM\UniqueConstraint(name="fournisseur_secteur_unique", columns={"fournisseur", "secteur"})}, indexes={@ORM\Index(name="fk_fournisseur_secteur_fournisseur1_idx", columns={"fournisseur"}), @ORM\Index(name="fk_fournisseur_secteur_Secteur1_idx", columns={"secteur"})})
 * @ORM\Entity
 */
class FournisseurSecteur
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
     * @var \Fournisseur
     *
     * @ORM\ManyToOne(targetEntity="Fournisseur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fournisseur", referencedColumnName="id")
     * })
     */
    private $fournisseur;

    /**
     * @var \Secteur
     *
     * @ORM\ManyToOne(targetEntity="Secteur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="secteur", referencedColumnName="id")
     * })
     */
    private $secteur;

    public function getId()
    {
        return $this->id;
    }

    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    public function setFournisseur($fournisseur): self
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getSecteur()
    {
        return $this->secteur;
    }

    public function setSecteur($secteur): self
    {
        $this->secteur = $secteur;

        return $this;
    }


}
