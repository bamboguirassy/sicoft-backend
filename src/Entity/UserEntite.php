<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserEntite
 *
 * @ORM\Table(name="user_entite", uniqueConstraints={@ORM\UniqueConstraint(name="user_entite_unique", columns={"entite", "user"})}, indexes={@ORM\Index(name="fk_user_entite_entite1_idx", columns={"entite"}), @ORM\Index(name="fk_user_entite_user1_idx", columns={"user"})})
 * @ORM\Entity
 */
class UserEntite
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
     * @var \Entite
     *
     * @ORM\ManyToOne(targetEntity="Entite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="entite", referencedColumnName="id")
     * })
     */
    private $entite;

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

    public function getEntite()
    {
        return $this->entite;
    }

    public function setEntite($entite): self
    {
        $this->entite = $entite;

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
