<?php

namespace CP\CompetitionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CP\CompetitionBundle\Entity\Game;
use Symfony\Component\HttpFoundation\Request;

class AdminManageTournoisController extends Controller
{
    public function adminViewGamesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //Etat 0 en attente
        //Etat 1 a jouer
        //Etat 2 joué
        //Etat 3 validé
        $listGamesOver = $em->getRepository('CPCompetitionBundle:Game')->findBy(array('etat' => '3'));
        $listGamesPending = $em->getRepository('CPCompetitionBundle:Game')->findBy(array('etat' => '0'));
        $listGamesTBP = $em->getRepository('CPCompetitionBundle:Game')->findby(array('etat' => '1'));
        $listGamesPlayed = $em->getRepository('CPCompetitionBundle:Game')->findBy(array('etat' => '2'));



        return $this->render('CPCompetitionBundle:AdminManageTournois:adminViewGames.html.twig', array(
            'gamesOver' => $listGamesOver,
            'gamesPending' => $listGamesPending,
            'gamesTBP' => $listGamesTBP,
            'gamesPlayed' => $listGamesPlayed
            ));
    }

}