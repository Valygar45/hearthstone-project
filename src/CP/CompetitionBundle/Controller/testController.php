<?php

namespace CP\CompetitionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class testController extends Controller
{
    public function indexAction($name)
    {



        return $this->render('CPCompetitionBundle:Default:index.html.twig', array('name' => $name));
    }
}
