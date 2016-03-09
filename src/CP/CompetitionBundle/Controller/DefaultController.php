<?php

namespace CP\CompetitionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CPCompetitionBundle:Default:tree.html.twig', array('name' => $name));
    }
}
