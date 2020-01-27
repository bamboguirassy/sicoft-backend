<?php
// src/Entity/User.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group")
     * @ORM\JoinTable(name="fos_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Entite")
     * @ORM\JoinTable(name="user_entite",
     *      joinColumns={@ORM\JoinColumn(name="user", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="entite", referencedColumnName="id")}
     * )
     */
    protected $entites;
    
    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=145, nullable=true)
     */
    private $prenom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=145, nullable=true)
     */
    private $nom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=145, nullable=true)
     */
    private $telephone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fonction", type="string", length=45, nullable=true)
     */
    private $fonction;
    
    /**
     * @var string
     *
     * @ORM\Column(name="photo_url", type="string", length=145, nullable=true)
     */
    private $photoUrl;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\EtatMarche")
     * @ORM\JoinTable(name="role_sur_marche",
     *      joinColumns={@ORM\JoinColumn(name="user", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="etat_marche", referencedColumnName="id")}
     * )
     */
    protected $etatMarches;
    

    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->entites = new ArrayCollection();
        $this->etatMarches = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom): self
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

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function getFonction()
    {
        return $this->fonction;
    }

    public function setFonction($fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl($photoUrl): self
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    /**
     * @return Collection|Entite[]
     */
    public function getEntites(): Collection
    {
        return $this->entites;
    }

    /**
     * @return Collection|EtatMarche[]
     */
    public function getEtatMarches(): Collection
    {
        return $this->etatMarches;
    }
}
