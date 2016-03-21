<?php

namespace CP\CompetitionBundle\Controller;

use CP\CompetitionBundle\Entity\Versus;
use CP\CompetitionBundle\Form\GameType;
use CP\CompetitionBundle\Form\VersusType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CP\CompetitionBundle\Entity\Game;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Collections\ArrayCollection;

class PlayerManageGamesController extends Controller
{
    public function manageErrorsVersusAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $listGames = $em
            ->getRepository('CPCompetitionBundle:Game')
            ->findBy(array('joueur1' => $user));
        $listGames2 = $em
            ->getRepository('CPCompetitionBundle:Game')
            ->findBy(array('joueur2' => $user));
        $listGamesMerged = new ArrayCollection(
            array_merge($listGames, $listGames2));
        $listConflictualVersus = array();

        foreach($listGamesMerged as $game)
        {
            $listConflictualVersus[] = $em
                ->getRepository('CPCompetitionBundle:Versus')
                ->findBy(array('game' => $game, 'etat' => 2));
        }
        return $this->render('CPCompetitionBundle:PlayerManageGames:manageErrorsVersus.html.twig', array('conflictualVersus' => $listConflictualVersus));
    }

}