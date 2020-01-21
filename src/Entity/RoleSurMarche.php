<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoleSurMarche
 *
 * @ORM\Table(name="role_sur_marche", uniqueConstraints={@ORM\UniqueConstraint(name="etat_marche_user_unique", columns={"etat_marche", "user"})}, indexes={@ORM\Index(name="fk_role_sur_marche_etat_marche1_idx", columns={"etat_marche"}), @ORM\Index(name="fk_role_sur_marche_fos_user1_idx", columns={"user"})})
 * @ORM\Entity
 */
class RoleSurMarche
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
     * @var \EtatMarche
     *
     * @ORM\ManyToOne(targetEntity="EtatMarche")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_marche", referencedColumnName="id")
     * })
     */
    private $etatMarche;

    /**
     * @var \FosUser
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    private $user;

    public function getId()
    {
        return $this->id;
    }

    public function getEtatMarche()
    {
        return $this->etatMarche;
    }

    public function setEtatMarche($etatMarche): self
    {
        $this->etatMarche = $etatMarche;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }


}
