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
}
