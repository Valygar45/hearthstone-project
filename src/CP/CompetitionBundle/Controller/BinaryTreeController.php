<?php

namespace CP\CompetitionBundle\Controller;

use CP\CompetitionBundle\Entity\Competition;
use CP\CompetitionBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CP\CompetitionBundle\Entity\Round;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


/**
 *
 *
 * Class BinaryTreeController
 * @package CP\CompetitionBundle\Controller
 */
class BinaryTreeController extends Controller
{
    /**
     * Recupère la competition donnée en argument et génère le tableau JSON permettant ensuite d'afficher l'arbre avec l'aide de JBracket
     *
     * @param Competition $competition
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function treeAction(Competition $competition)
    {
        $tree = $this->container->get('cp_competition.binarytree');
        if($competition->getType()=="simple"){
            $jstab = $tree->SimpleTreeJS($competition->getId());
        }
        else if($competition->getType()=="double"){
            $jstab = $tree->DoubleTreeJS($competition->getId());
        }

        return $this->render('CPCompetitionBundle:BinaryTree:tree.html.twig',array("bracketJSON"=>$jstab,"competitionType"=>$competition->getType()));
    }

    /**
     * Permet de créer une nouvelle compétition et de génerer l'arbre associé.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
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
            ->add('type', 'choice', array(
                'choices'  => array(
                    'simple' => "Simple Elimination Bracket",
                    'double' => "Double Elimination Bracket",

                ),
            ))
        ;


        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // On fait le lien Requête <-> Formulaire

        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($competition);
            $tree = $this->container->get('cp_competition.binarytree');
            $players=array("Marseille1","Paris2","Monaco3","Caen4","Toulouse5","Troyes6","Barcelone7","Juventus8","Marseille1","Paris2","Monaco3","Caen4","Toulouse5","Troyes6","Barcelone7","Juventus8","Marseille1","Paris2","Monaco3","Caen4","Toulouse5","Troyes6","Barcelone7","Juventus8","Marseille1","Paris2","Monaco3","Caen4","Toulouse5","Troyes6","Barcelone7","Juventus8");

            if($competition->getType()=="simple"){
                $fatherRound = $tree->simpleTreeGenerator($competition->getNbPlayers(),$players);
            }
            else if($competition->getType()=="double"){
                $fatherRound = $tree->doubleTreeGenerator($competition->getNbPlayers(),$players);
            }

            $em->persist($fatherRound);
            $competition->setFatherRound($fatherRound);
            $em->flush();


            return $this->redirect($this->generateUrl('cp_competition_tree',array('id'=>$competition->getId())));

            }

// On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('CPCompetitionBundle:BinaryTree:new.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * Formulaire qui va permettre d'afficher un Game et de le modifier si les droits de l'utilisateur le permet.
     *
     * @param Request $request
     * @param $game_id
     * @param Competition $competition
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function gameAction(Request $request, $game_id, Competition $competition)
    {
        // On crée un objet Game
        $repository = $this->getDoctrine()->getManager()->getRepository('CPCompetitionBundle:Game');
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

            $tree = $this->container->get('cp_competition.binarytree');
            $emanage = $this->getDoctrine()->getManager();
            $emanage->persist($game);
            if($competition->getType()=="simple"){
                $tree->game_valid_simple($emanage,$game);
            }
            else if($competition->getType()=="double"){
                $tree->game_valid_double($emanage,$game);
            }


            return $this->redirect($this->generateUrl('cp_competition_tree',array("id"=>$competition->getId())));
        }


        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('CPCompetitionBundle:BinaryTree:game.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
