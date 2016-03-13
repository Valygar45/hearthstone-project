<?php

namespace CP\CompetitionBundle\Controller;

use CP\CompetitionBundle\Entity\Competition;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class RoundRobinController extends Controller
{
    public function indexAction()
    {

        $roundRobin = $this->container->get('cp_competition.roundrobin');
        $players=array("Marseille1","Paris2","Monaco3","Caen4","Toulouse5","Troyes6","Barcelone7","Juventus8","Marseille1","Paris2","Monaco3","Caen4","Toulouse5","Troyes6","Barcelone7","Juventus8","Marseille1","Paris2","Monaco3","Caen4","Toulouse5","Troyes6","Barcelone7","Juventus8","Marseille1","Paris2","Monaco3","Caen4","Toulouse5","Troyes6","Barcelone7","Juventus8");
       $competition = new Competition();
        $competition->setName("test");
        $competition->setDescription("test");
        $competition->setNbPlayers(16);
        $competition->setNews("test");
        $competition->setState("test");
        $competition->setType("test");



       $tab= $roundRobin->roundRobinGenerator($competition,$players,16);

        return $this->render('CPCompetitionBundle:RoundRobin:roundRobin.html.twig', array('tab' => $tab));
    }

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

        return $this->render('CPCompetitionBundle:RoundRobin:roundRobinView.html.twig', array('competition' => $competition,'rank'=>$ranking));
    }
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
            ->add('team1',      'text')
            ->add('team2',     'text')
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
}
