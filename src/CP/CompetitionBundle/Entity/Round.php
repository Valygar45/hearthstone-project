<?php

namespace CP\CompetitionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Round
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CP\CompetitionBundle\Entity\RoundRepository")
 */
class Round
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
     * @ORM\Column(name="numRound", type="integer")
     */
    private $numRound;

    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param mixed $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }


    /**
     * @ORM\ManyToOne(targetEntity="CP\CompetitionBundle\Entity\Game",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="CP\CompetitionBundle\Entity\Round")
     * @ORM\JoinColumn(nullable=true)
     */
    private $parentRound;

    /**
     * @return mixed
     */
    public function getRightRound()
    {
        return $this->rightRound;
    }

    /**
     * @param mixed $rightRound
     */
    public function setRightRound($rightRound)
    {
        $this->rightRound = $rightRound;
    }

    /**
     * @return mixed
     */
    public function getParentRound()
    {
        return $this->parentRound;
    }

    /**
     * @param mixed $parentRound
     */
    public function setParentRound($parentRound)
    {
        $this->parentRound = $parentRound;
    }

    /**
     * @ORM\ManyToOne(targetEntity="CP\CompetitionBundle\Entity\Round")
     * @ORM\JoinColumn(nullable=true)
     */
    private $leftRound;

    /**
     * @return mixed
     */
    public function getLeftRound()
    {
        return $this->leftRound;
    }

    /**
     * @param mixed $leftRound
     */
    public function setLeftRound($leftRound)
    {
        $this->leftRound = $leftRound;
    }

    /**
     * @ORM\ManyToOne(targetEntity="CP\CompetitionBundle\Entity\Round")
     * @ORM\JoinColumn(nullable=true)
     */
    private $rightRound;

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
     * Set numRound
     *
     * @param integer $numRound
     * @return Round
     */
    public function setNumRound($numRound)
    {
        $this->numRound = $numRound;

        return $this;
    }

    /**
     * Get numRound
     *
     * @return integer 
     */
    public function getNumRound()
    {
        return $this->numRound;
    }

    public function __construct()
    {
        $this->numRound = 0;

    }



}
