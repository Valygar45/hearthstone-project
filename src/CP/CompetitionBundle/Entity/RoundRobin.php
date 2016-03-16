<?php

namespace CP\CompetitionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RoundRobin
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CP\CompetitionBundle\Entity\RoundRobinRepository")
 */
class RoundRobin
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbJoueurs", type="integer")
     */
    private $nbJoueurs;
    /**
     * @ORM\ManyToMany(targetEntity="CP\CompetitionBundle\Entity\Round", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $rounds;

    /**
     * @return mixed
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * @return mixed
     */
    public function setRounds($rounds)
    {
        $this->rounds=$rounds;
    }

    /**
     * @param mixed $round
     */
    public function addRound(Round $round)
    {
        $this->rounds[]  = $round;
        return $this;
    }

    public function removeRound(Round $round){
        $this->rounds->removeElement($round);

    }

    public function __construct()
    {

        $this->rounds = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return RoundRobin
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nbJoueurs
     *
     * @param integer $nbJoueurs
     * @return RoundRobin
     */
    public function setNbJoueurs($nbJoueurs)
    {
        $this->nbJoueurs = $nbJoueurs;

        return $this;
    }

    /**
     * Get nbJoueurs
     *
     * @return integer 
     */
    public function getNbJoueurs()
    {
        return $this->nbJoueurs;
    }
}
