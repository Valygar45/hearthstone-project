<?php
// src/CP/UserBundle/Entity/User.php

namespace CP\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
        // Par défaut, la date de l'annonce est la date d'aujourd'hui
        $this->date_inscription = new \Datetime();
        $this->palmares = 'N;';
    }

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="date_inscription", type="date")
     *
     */
    protected $date_inscription;

    /**
     * @ORM\Column(name="palmares", type="array")
     */
    protected $palmares;
}