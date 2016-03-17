<?php

namespace CP\CompetitionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Competition
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CP\CompetitionBundle\Entity\CompetitionRepository")
 */
class Competition
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
     * @ORM\ManyToOne(targetEntity="CP\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $creator;

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param mixed $creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="news", type="string", length=255)
     */
    private $news;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     * @ORM\Column(name="dateCreate", type="date",  nullable=true)
     */
    private $dateCreate;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPlayers", type="integer")
     */
    private $nbPlayers;

    /**
     * Get id
     *
     * @return integer 
     */

    /**
     * @ORM\ManyToOne(targetEntity="CP\CompetitionBundle\Entity\Round")
     * @ORM\JoinColumn(nullable=true)
     */
    private $fatherRound;



    /**
     * @ORM\ManyToMany(targetEntity="CP\CompetitionBundle\Entity\RoundRobin", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $roundRobins;

    /**
     * @return mixed
     */
    public function getRoundRobins()
    {
        return $this->roundRobins;
    }

    /**
     * @param mixed $roundRobin
     */
    public function addRoundRobin(RoundRobin $roundRobin)
    {
        $this->roundRobins[]  = $roundRobin;
        return $this;
    }

    public function removeRoundRobin(RoundRobin $roundRobin){
    $this->roundRobins->removeElement($roundRobin);
}

    public function __construct()
    {

        $this->roundRobins = new ArrayCollection();
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\ManyToMany(targetEntity="CP\UserBundle\Entity\User", cascade={"persist"})
     */
    private $players;

    /**
     * @return mixed
     */
    public function getFatherRound()
    {
        return $this->fatherRound;
    }

    /**
     * @param mixed $fatherRound
     */
    public function setFatherRound($fatherRound)
    {
        $this->fatherRound = $fatherRound;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Competition
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
     * Set description
     *
     * @param string $description
     * @return Competition
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set news
     *
     * @param string $news
     * @return Competition
     */
    public function setNews($news)
    {
        $this->news = $news;

        return $this;
    }

    /**
     * Get news
     *
     * @return string 
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Competition
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return Competition
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime 
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Competition
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set nbPlayers
     *
     * @param integer $nbPlayers
     * @return Competition
     */
    public function setNbPlayers($nbPlayers)
    {
        $this->nbPlayers = $nbPlayers;

        return $this;
    }

    /**
     * Get nbPlayers
     *
     * @return integer 
     */
    public function getNbPlayers()
    {
        return $this->nbPlayers;
    }



    /**
     * Add players
     *
     * @param \CP\UserBundle\Entity\User $players
     * @return Competition
     */
    public function addPlayer(\CP\UserBundle\Entity\User $players)
    {
        $this->players[] = $players;

        return $this;
    }

    /**
     * Remove players
     *
     * @param \CP\UserBundle\Entity\Users $players
     */
    public function removePlayer(\CP\UserBundle\Entity\User $players)
    {
        $this->players->removeElement($players);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlayers()
    {
        return $this->players;
    }
}
