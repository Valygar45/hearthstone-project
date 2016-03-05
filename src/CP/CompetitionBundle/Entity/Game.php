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
     * @return string
     */
    public function getTeam2()
    {
        return $this->team2;
    }

    /**
     * @param string $team2
     */
    public function setTeam2($team2)
    {
        $this->team2 = $team2;
    }

    /**
     * @return string
     */
    public function getTeam1()
    {
        return $this->team1;
    }

    /**
     * @param string $team1
     */
    public function setTeam1($team1)
    {
        $this->team1 = $team1;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="team1", type="string", length=255)
     */
    private $team1;

    /**
     * @var string
     *
     * @ORM\Column(name="team2", type="string", length=255)
     */
    private $team2;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;


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
     * Set score
     *
     * @param integer $score
     * @return Game
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }
}
