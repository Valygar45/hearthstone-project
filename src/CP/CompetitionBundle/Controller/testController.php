<?php

namespace CP\CompetitionBundle\Controller;

use CP\CompetitionBundle\Entity\Competition;
use CP\CompetitionBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CP\CompetitionBundle\Entity\Round;
use Symfony\Component\HttpFoundation\Request;


class testController extends Controller
{
    public function indexAction(Competition $competition)
    {
        $tree = $this->container->get('cp_competition.binarytree');
        $jstab = $tree->simpleTreeJS($competition->getId());
        return $this->render('CPCompetitionBundle:index.html.twig',array("tabtest"=>$jstab));
    }

    public function newAction(Request $request)
    {
        $competition = new Competition();
        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder('form', $competition);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('name',      'text')
            ->add('description',     'text')
            ->add('news',   'text')
            ->add('type',    'text')
            ->add('dateCreate',      'datetime')
            ->add('state',      'text')
            ->add('nbPlayers',      'integer')
            ->add('Créer',      'submit')
        ;


        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid()) {
            // On l'enregistre notre objet $advert dans la base de données, par exemple
            $em = $this->getDoctrine()->getManager();
            $em->persist($competition);
            $tree = $this->container->get('cp_competition.binarytree');
            $fatherRound = $tree->simpleTreeGenerator($competition->getNbPlayers());
            $em->persist($fatherRound);
            $competition->setFatherRound($fatherRound);
            $em->flush();

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirect($this->generateUrl('cp_competition_tree',array('id_competition'=>$competition->getId())));

            }

// On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('CPCompetitionBundle:game.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    public function gameAction(Request $request,$game_id,Competition $competition)
    {
        // On crée un objet Game
        $repository = $this->getDoctrine()->getManager()->getRepository('CPCompetitionBundle:Game');
        $game = $repository->find($game_id);

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder('form', $game);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('team1',      'text')
            ->add('team2',     'text')
            ->add('score1',   'text')
            ->add('score2',    'text')
            ->add('save',      'submit')
        ;


        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid()) {
            // On l'enregistre notre objet $advert dans la base de données, par exemple
            $em = $this->getDoctrine()->getManager();
            $em->persist($game);

            if($game->getScore1()>$game->getScore2()){
                $winner = $game->getTeam1();
            }
            else if($game->getScore1()==$game->getScore2()){
                $winner = false;
            }
            else {
                $winner = $game->getTeam2();
            }

            $round = $em->getRepository('CPCompetitionBundle:Round')->findOneByGame($game_id);

            if($round != null && $winner !=false) {
                $nextRound = $round->getParentRound();
                if ($nextRound != null) {

                    $em->persist($nextRound);
                    $nextGame = $nextRound->getGame();
                    if ($nextGame == null) {
                        $nextGame = new Game();
                    }

                    $em->persist($nextGame);
                    $nextRound->setGame($nextGame);
                    if ($nextRound->getRightRound() == $round) {

                        $nextGame->setTeam1($winner);
                    } else {
                        $nextGame->setTeam2($winner);
                    }
                }
            }

            $em->flush();
            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirect($this->generateUrl('cp_competition_tree',array("id"=>$competition->getId())));
        }


        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('CPCompetitionBundle:game.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
