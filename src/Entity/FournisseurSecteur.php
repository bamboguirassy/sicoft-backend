<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FournisseurSecteur
 *
 * @ORM\Table(name="fournisseur_secteur", uniqueConstraints={@ORM\UniqueConstraint(name="fournisseur_secteur_unique", columns={"fournisseur", "secteur"})}, indexes={@ORM\Index(name="fk_fournisseur_secteur_Secteur1_idx", columns={"secteur"}), @ORM\Index(name="fk_fournisseur_secteur_fournisseur1_idx", columns={"fournisseur"})})
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
     * @var int|null
     *
     * @ORM\Column(name="fournisseur", type="integer", nullable=true)
     */
    private $fournisseur;

    /**
     * @var int|null
     *
     * @ORM\Column(name="secteur", type="integer", nullable=true)
     */
    private $secteur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFournisseur(): ?int
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?int $fournisseur): self
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getSecteur(): ?int
    {
        return $this->secteur;
    }

    public function setSecteur(?int $secteur): self
    {
        $this->secteur = $secteur;

        return $this;
    }


}
