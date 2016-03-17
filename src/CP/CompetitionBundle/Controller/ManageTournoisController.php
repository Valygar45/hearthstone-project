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
            array_merge($listGames, $listGames2))
        ;



        return $this->render('CPCompetitionBundle:ManageTournois:viewGames.html.twig', array('games' => $listGamesMerged));
    }

    public function uploadScreenshotsAction($id, Request $request)
    {
        //nombre de versus ayant eu lieu pour la game
        $nbVersus = $request->get('nbVersus');
        //on récupère la game concernée
        $em = $this->getDoctrine()->getManager();
        $game = $em->getRepository('CPCompetitionBundle:Game')->find($id);
        //on créé les versus dynamiquement pour en avoir autant qu'il y en a eu puis on les ajoute au form
        for($nbVersusAdded=0; $nbVersusAdded<$nbVersus; $nbVersusAdded++)
        {
            //creation de l'objet versus
            ${'versus'.$nbVersusAdded} = new Versus();
            //ajout du numéro du versus
            ${'versus'.$nbVersusAdded}->setNumber($nbVersusAdded);

            $game->addVersuss(${'versus'.$nbVersusAdded});
        }
        $form = $this->get('form.factory')->create(new GameType(), $game);
        /*//Test avec un seul versus
        //creation d'un versus
        $versus = new Versus();
        $versus->setGame($game);
        //creation du formulaire correspondant au versus
        $form = $this->get('form.factory')->create(new VersusType, $versus);
        */
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush();

            $session = $this->getRequest()->getSession();
            $session->getFlashBag()->add('message', 'Game saved');

            return $this->redirect($this->generateUrl('cp_competition_viewGames:'));
        }


        return $this->render('CPCompetitionBundle:ManageTournois:uploadScreenshots.html.twig', array(
            'form' => $form->createView(),
        ));

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
        //$game->setEtat(3); //a decommenter quand tu veux que le statut de la game passe en validé

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