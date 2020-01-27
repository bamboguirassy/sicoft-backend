<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeClasse
 *
 * @ORM\Table(name="type_classe")
 * @ORM\Entity
 */
class TypeClasse
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
     * @ORM\Column(name="code", type="string", length=45, nullable=false, options={"comment"="1 ou 2"})
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=false, options={"comment"="1 pour recette, 2 pour depense"})
     */
    private $nom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }


}
