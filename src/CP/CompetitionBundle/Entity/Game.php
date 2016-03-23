<?php

namespace CP\CompetitionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CP\CompetitionBundle\Entity\GameRepository")
 */
class Game
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="score1", type="integer",nullable=true)
     */
    private $score1;

    /**
     * @var integer
     *
     * @ORM\Column(name="score2", type="integer",nullable=true)
     */
    private $score2;

    /**
     * @ORM\ManyToOne(targetEntity="CP\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $joueur1;

    /**
     * @ORM\ManyToOne(targetEntity="CP\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $joueur2;

    /**
     * @var integer
     *
     * @ORM\Column(name="etat", type="integer", nullable=true)
     */
    private $etat;

    /**
     * @var \CP\CompetitionBundle\Entity\Versus
     *
     * @ORM\OneToMany(targetEntity="CP\CompetitionBundle\Entity\Versus", mappedBy="game", cascade={"persist"})
     */
    private $Versuss;

    /**
     * @ORM\ManyToOne(targetEntity="CP\CompetitionBundle\Entity\Competition")
     * @ORM\JoinColumn(nullable=false)
     */
    private $competition;

    /**
     * @return mixed
     */
    public function getCompetition()
    {
        return $this->competition;
    }

    /**
     * @param mixed $competition
     */
    public function setCompetition($competition)
    {
        $this->competition = $competition;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set score1
     *
     * @param integer $score
     * @return Game
     */
    public function setScore1($score)
    {
        $this->score1 = $score;

        return $this;
    }

    /**
     * Set score2
     *
     * @param integer $score
     * @return Game
     */
    public function setScore2($score)
    {
        $this->score2 = $score;

        return $this;
    }

    /**
     * Get score1
     *
     * @return integer 
     */
    public function getScore1()
    {
        return $this->score1;
    }

    /**
     * Get score2
     *
     * @return integer
     */
    public function getScore2()
    {
        return $this->score2;
    }

    /**
     * Set joueur1
     *
     * @param \CP\UserBundle\Entity\User $joueur1
     * @return Game
     */
    public function setJoueur1(\CP\UserBundle\Entity\User $joueur1 = null)
    {
        $this->joueur1 = $joueur1;

        return $this;
    }

    /**
     * Get joueur1
     *
     * @return \CP\UserBundle\Entity\User 
     */
    public function getJoueur1()
    {
        return $this->joueur1;
    }

    /**
     * Set joueur2
     *
     * @param \CP\UserBundle\Entity\User $joueur2
     * @return Game
     */
    public function setJoueur2(\CP\UserBundle\Entity\User $joueur2 = null)
    {
        $this->joueur2 = $joueur2;

        return $this;
    }

    /**
     * Get joueur2
     *
     * @return \CP\UserBundle\Entity\User 
     */
    public function getJoueur2()
    {
        return $this->joueur2;
    }

    /**
     * Set etat
     *
     * @param integer $etat
     * @return Game
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return integer 
     */
    public function getEtat()
    {
        return $this->etat;
    }
    /**
     * Constructor
     */
    public function __construct($competition)
    {
        $this->Versuss = new \Doctrine\Common\Collections\ArrayCollection();
        $this->competition=$competition;
    }

    /**
     * Add Versuss
     *
     * @param \CP\CompetitionBundle\Entity\Versus $versuss
     * @return Game
     */
    public function addVersuss(\CP\CompetitionBundle\Entity\Versus $versuss)
    {
        $this->Versuss->add($versuss);

        return $this;
    }

    /**
     * Remove Versuss
     *
     * @param \CP\CompetitionBundle\Entity\Versus $versuss
     */
    public function removeVersuss(\CP\CompetitionBundle\Entity\Versus $versuss)
    {
        $this->Versuss->removeElement($versuss);
    }

    /**
     * Get Versus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVersuss()
    {
        return $this->Versuss;
    }
}
