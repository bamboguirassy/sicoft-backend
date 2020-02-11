<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tracelog
 *
 * @ORM\Table(name="tracelog")
 * @ORM\Entity
 */
class Tracelog
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $date = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="ressource", type="string", length=50, nullable=false)
     */
    private $ressource;

    /**
     * @var string
     *
     * @ORM\Column(name="operation", type="string", length=20, nullable=false)
     */
    private $operation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="oldValue", type="text", length=0, nullable=true)
     */
    private $oldvalue;

    /**
     * @var string|null
     *
     * @ORM\Column(name="newValue", type="text", length=0, nullable=true)
     */
    private $newvalue;

    /**
     * @var string
     *
     * @ORM\Column(name="user_email", type="string", length=100, nullable=false)
     */
    private $userEmail;

    public function getId()
    {
        return $this->id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getRessource()
    {
        return $this->ressource;
    }

    public function setRessource(string $ressource): self
    {
        $this->ressource = $ressource;

        return $this;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function setOperation(string $operation): self
    {
        $this->operation = $operation;

        return $this;
    }

    public function getOldvalue()
    {
        return $this->oldvalue;
    }

    public function setOldvalue($oldvalue): self
    {
        $this->oldvalue = $oldvalue;

        return $this;
    }

    public function getNewvalue()
    {
        return $this->newvalue;
    }

    public function setNewvalue($newvalue): self
    {
        $this->newvalue = $newvalue;

        return $this;
    }

    public function getUserEmail()
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): self
    {
        $this->userEmail = $userEmail;

        return $this;
    }


}
