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
    private $score1;

    /**
     * @var integer
     *
     * @ORM\Column(name="score2", type="integer")
     */
    private $score2;


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
}
