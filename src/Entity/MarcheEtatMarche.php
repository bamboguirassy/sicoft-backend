<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarcheEtatMarche
 *
 * @ORM\Table(name="marche_etat_marche", uniqueConstraints={@ORM\UniqueConstraint(name="marche_etat_marche_unique", columns={"marche", "etat_marche"})}, indexes={@ORM\Index(name="fk_marche_etat_marche_etat_marche1_idx", columns={"etat_marche"}), @ORM\Index(name="fk_marche_etat_marche_marche1_idx", columns={"marche"}), @ORM\Index(name="fk_marche_etat_marche_fos_user1_idx", columns={"user"})})
 * @ORM\Entity
 */
class MarcheEtatMarche
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
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaire", type="text", length=65535, nullable=true)
     */
    private $commentaire;

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
     * @ORM\ManyToOne(targetEntity="FosUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Marche
     *
     * @ORM\ManyToOne(targetEntity="Marche")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="marche", referencedColumnName="id")
     * })
     */
    private $marche;


}
