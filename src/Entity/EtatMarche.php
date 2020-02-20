<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * EtatMarche
 *
 * @ORM\Table(name="etat_marche", uniqueConstraints={@ORM\UniqueConstraint(name="code_etat_UNIQUE", columns={"code"})}, indexes={@ORM\Index(name="fk_etat_marche_etat_marche1_idx", columns={"etat_suivant"}), @ORM\Index(name="type_passation", columns={"type_passation"})})
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
     * @var \TypePassation
     *
     * @ORM\ManyToOne(targetEntity="TypePassation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_passation", referencedColumnName="id")
     * })
     */
    private $typePassation;

    /**
     * @var \EtatMarche
     *
     * @ORM\ManyToOne(targetEntity="EtatMarche")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_suivant", referencedColumnName="id")
     * })
     */
    private $etatSuivant;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(name="role_sur_marche",
     *      joinColumns={@ORM\JoinColumn(name="etat_marche", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user", referencedColumnName="id")}
     * )
     */
    protected $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTypePassation(): ?TypePassation
    {
        return $this->typePassation;
    }

    public function setTypePassation(?TypePassation $typePassation): self
    {
        $this->typePassation = $typePassation;

        return $this;
    }

    public function getEtatSuivant(): ?self
    {
        return $this->etatSuivant;
    }

    public function setEtatSuivant(?self $etatSuivant): self
    {
        $this->etatSuivant = $etatSuivant;

        return $this;
    }

    /**
     * @return Collection|FosUser[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(FosUser $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(FosUser $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
        }

        return $this;
    }

}
