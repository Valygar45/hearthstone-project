<?php

namespace CP\CompetitionBundle\Controller;

use CP\CompetitionBundle\Entity\Competition;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class RoundRobinController extends Controller
{
    /**
     * Permet de visualier les round robin d'une competition
     * Le tableau retourné est indexé par roundRobin puis par journée de la roundRobin
     *
     * @param Competition $competition
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function viewAction(Competition $competition)
    {
        $roundRobinService = $this->container->get('cp_competition.roundrobin');
        $ranking = $roundRobinService->roundRobinRankingSQL($competition);

    foreach($competition->getRoundRobins()as $roundRobin){

        $rounds = array();
        foreach($roundRobin->getRounds() as $round){
            $rounds[$round->getNumRound()][]=$round;
        }
        $roundRobin->setRounds($rounds);
    }


        $gamesUncomplete = $roundRobinService->findUncompleteGameRoundRobin($competition);
//On regarde si tous les games ont été jouées, si c'est le cas cela veut dire qu'il est possible de génerer l'arbre des phases finales
        if( empty( $gamesUncomplete)){
            $generable = true;
        }
        else{
            $generable=false;
        }

        return $this->render('CPCompetitionBundle:RoundRobin:roundRobinView.html.twig', array('competition' => $competition,'rank'=>$ranking, "generable"=>$generable));
    }

    /**
     * Permet d'hydrater un objet de type Game selon les conditions de roundRobin
     *
     * @param Request $request
     * @param $game_id
     * @param Competition $competition
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function gameAction(Request $request, $game_id, Competition $competition)
    {
        $em=$this->getDoctrine()->getManager();
        // On crée un objet Game
        $repository = $em->getRepository('CPCompetitionBundle:Game');
        $game = $repository->find($game_id);
        if($game==null){
            throw new NotFoundHttpException('Ce match n\'existe pas.');
        }

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder('form', $game);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('joueur1',      'text')
            ->add('joueur2',     'text')
            ->add('score1',   'text')
            ->add('score2',    'text')
            ->add('save',      'submit')
        ;


        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // On fait le lien Requête <-> Formulaire

        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes

        if ($form->isValid()) {

            $roundRobin = $this->container->get('cp_competition.roundrobin');
            $emanage = $this->getDoctrine()->getManager();
            $emanage->persist($game);

                $roundRobin->game_valid_simple($emanage,$game);


            return $this->redirect($this->generateUrl('cp_competition_roundrobinview',array("id"=>$competition->getId())));
        }


        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('CPCompetitionBundle:BinaryTree:game.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *
     *Permet de génerer l'arbre du tournoi en selectionnant les qualifiés
     * @param Competition $competition
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function generateAction(Competition $competition){
        $roundRobinService = $this->container->get('cp_competition.roundrobin');
        $ranking = $roundRobinService->roundRobinRankingSQL($competition);

        $em = $this->getDoctrine()->getManager();
        $em->persist($competition);
        $tree = $this->container->get('cp_competition.binarytree');

        $players = array();

       foreach($ranking  as $roundrobinRank){
            $players[0][]=$roundrobinRank[0]["team"];
            $players[1][]=$roundrobinRank[1]["team"];
        }
        shuffle( $players[0]);
        shuffle( $players[1]);
        $playersOrdered = array();
        for($i=0;$i<count($players[0]);$i++){
            $playersOrdered[]=$players[0][$i];
            $playersOrdered[]=$players[1][$i];
        }


        $competitionType = $competition->getType();
        if($competitionType=="roundRobinSimple"){
            $fatherRound = $tree->simpleTreeGenerator(count($playersOrdered),$playersOrdered);
        }
        else if ($competitionType=="roundRobinDouble"){
            $fatherRound = $tree->doubleTreeGenerator(count($playersOrdered),$playersOrdered);
        }

        $em->persist($fatherRound);
        $competition->setFatherRound($fatherRound);
        $em->flush();

        return $this->redirect($this->generateUrl('cp_competition_tree',array('id'=>$competition->getId())));

    }
}
