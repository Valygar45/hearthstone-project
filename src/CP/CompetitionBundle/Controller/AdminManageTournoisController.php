<?php

namespace CP\CompetitionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CP\CompetitionBundle\Entity\Game;
use CP\CompetitionBundle\Entity\Versus;
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

    public function adminValidateScoreAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $game = $em
            ->getRepository('CPCompetitionBundle:Game')
            ->find($id)
        ;
        $versus = $em->getRepository('CPCompetitionBundle:Versus')->findBy(array('game' => $game));

        return $this->render('CPCompetitionBundle:AdminManageTournois:adminValidateScore.html.twig', array(
            'versus' => $versus,
            'game' => $game
        ));
    }

    public function adminSetScoreProblemsAction($id, Request $request)
    {
        $numbers = $request->get('number');
        $em = $this->getDoctrine()->getManager();
        if ($numbers != null)
        {
            foreach ($numbers as $n)
            {
                $versus = $em
                    ->getRepository('CPCompetitionBundle:Versus')
                    ->find($n);
                $versus->setEtat(2);
                $em->persist($versus);
            }
        }else{
            $game = $em
                ->getRepository('CPCompetitionBundle:Game')
                ->find($id)
            ;
            $game->setEtat(3);
            $em->persist($game);
        }
        $em->flush();


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