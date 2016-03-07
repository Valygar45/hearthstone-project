<?php

namespace CP\CompetitionBundle\Controller;

use CP\CompetitionBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CP\CompetitionBundle\Entity\Round;


class testController extends Controller
{
    public function indexAction()
    {
        $tree = $this->container->get('cp_competition.binarytree');
        $jstab = $tree->simpleTreeJS(1);
        return $this->render('CPCompetitionBundle:index.html.twig',array("tabtest"=>$jstab));
    }

}
