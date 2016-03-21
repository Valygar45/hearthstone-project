<?php

namespace CP\CompetitionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Versus
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CP\CompetitionBundle\Entity\VersusRepository")
 */
class Versus
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
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var integer
     *
     * @ORM\Column(name="scorej1", type="integer")
     */
    private $scorej1;

    /**
     * @var integer
     *
     * @ORM\Column(name="scorej2", type="integer")
     */
    private $scorej2;

    /**
     * @ORM\ManyToOne(targetEntity="CP\CompetitionBundle\Entity\Game", inversedBy="versus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\OneToOne(targetEntity="CP\CompetitionBundle\Entity\Screenshot", mappedBy="versus", cascade={"persist"})
     *
     */
    private $screenshot;

    /**
     * @var integer
     *
     * @ORM\Column(name="etat", type="integer")
     */
    private $etat;

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
     * Set number
     *
     * @param integer $number
     * @return Versus
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set scorej1
     *
     * @param integer $scorej1
     * @return Versus
     */
    public function setScorej1($scorej1)
    {
        $this->scorej1 = $scorej1;

        return $this;
    }

    /**
     * Get scorej1
     *
     * @return integer 
     */
    public function getScorej1()
    {
        return $this->scorej1;
    }

    /**
     * Set scorej2
     *
     * @param integer $scorej2
     * @return Versus
     */
    public function setScorej2($scorej2)
    {
        $this->scorej2 = $scorej2;

        return $this;
    }

    /**
     * Get scorej2
     *
     * @return integer 
     */
    public function getScorej2()
    {
        return $this->scorej2;
    }

    /**
     * Set game
     *
     * @param \CP\CompetitionBundle\Entity\Game $game
     * @return Versus
     */
    public function setGame(\CP\CompetitionBundle\Entity\Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \CP\CompetitionBundle\Entity\Game 
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set screenshot
     *
     * @param \CP\CompetitionBundle\Entity\Screenshot $screenshot
     * @return Versus
     */
    public function setScreenshot(\CP\CompetitionBundle\Entity\Screenshot $screenshot = null)
    {
        $this->screenshot = $screenshot;

        return $this;
    }

    /**
     * Get screenshot
     *
     * @return \CP\CompetitionBundle\Entity\Screenshot 
     */
    public function getScreenshot()
    {
        return $this->screenshot;
    }

    /**
     * Set etat
     *
     * @param integer $etat
     * @return Versus
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
}
