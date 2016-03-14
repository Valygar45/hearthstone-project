<?php

namespace CP\CompetitionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CP\CompetitionBundle\Entity\Game;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Collections\ArrayCollection;

class ManageTournoisController extends Controller
{
    public function viewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $listGames = $em
        ->getRepository('CPCompetitionBundle:Game')
        ->findBy(array('joueur1' => $user))
    ;

        $listGames2 = $em
            ->getRepository('CPCompetitionBundle:Game')
            ->findBy(array('joueur2' => $user))
        ;

        $listGamesMerged = new ArrayCollection(
            array_merge($listGames, $listGames2)
        );



        return $this->render('CPCompetitionBundle:ManageTournois:viewGames.html.twig', array('games' => $listGamesMerged));
    }

    public function validateScoreAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $game = $em
            ->getRepository('CPCompetitionBundle:Game')
            ->find($id)
        ;

        $game->setScore1($request->get('score1'));
        $game->setScore2($request->get('score2'));
        $game->setEtat(3);

        $em->persist($game);
        $em->flush();
        $user = $this->getUser();
        return $this->render('CPUserBundle:profil:profil.html.twig', array('user' => $user));
    }

    public function viewPastGamesAction()
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $listGames = $em
            ->getRepository('CPCompetitionBundle:Game')
            ->findBy(array('joueur1' => $user))
        ;
        $listGames2 = $em
            ->getRepository('CPCompetitionBundle:Game')
            ->findBy(array('joueur2' => $user))
        ;
        $listGamesMerged = new ArrayCollection(
            array_merge($listGames, $listGames2)
        );

        return $this->render('CPCompetitionBundle:ManageTournois:viewPastGames.html.twig', array('user' => $user, 'games' => $listGamesMerged));
    }

}