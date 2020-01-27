<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fournisseur
 *
 * @ORM\Table(name="fournisseur")
 * @ORM\Entity
 */
class Fournisseur
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
     * @ORM\Column(name="nom", type="string", length=100, nullable=false)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telephone", type="string", length=45, nullable=true)
     */
    private $telephone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="adresse", type="string", length=45, nullable=true)
     */
    private $adresse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ninea", type="string", length=45, nullable=true)
     */
    private $ninea;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_contact", type="string", length=45, nullable=true)
     */
    private $nomContact;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telephone_contact", type="string", length=45, nullable=true)
     */
    private $telephoneContact;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fonction_contact", type="string", length=45, nullable=true)
     */
    private $fonctionContact;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Secteur")
     * @ORM\JoinTable(name="fournisseur_secteur",
     *      joinColumns={@ORM\JoinColumn(name="fournisseur", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="secteur", referencedColumnName="id")}
     * )
     */
    protected $secteurs;
    
    public function __construct() {
        $this->secteurs = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone($telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function setAdresse($adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNinea()
    {
        return $this->ninea;
    }

    public function setNinea($ninea): self
    {
        $this->ninea = $ninea;

        return $this;
    }

    public function getNomContact()
    {
        return $this->nomContact;
    }

    public function setNomContact($nomContact): self
    {
        $this->nomContact = $nomContact;

        return $this;
    }

    public function getTelephoneContact()
    {
        return $this->telephoneContact;
    }

    public function setTelephoneContact($telephoneContact): self
    {
        $this->telephoneContact = $telephoneContact;

        return $this;
    }

    public function getFonctionContact()
    {
        return $this->fonctionContact;
    }

    public function setFonctionContact($fonctionContact): self
    {
        $this->fonctionContact = $fonctionContact;

        return $this;
    }


}
