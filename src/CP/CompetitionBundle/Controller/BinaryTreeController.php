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
        if($competition->getType()=="treeSimple" || $competition->getType()=='roundRobinSimple'){
            $jstab = $tree->SimpleTreeJS($competition->getId());
        }
        else if($competition->getType()=="treeDouble" || $competition->getType()=='roundRobinDouble'){
            $jstab = $tree->DoubleTreeJS($competition->getId());
        }

        return $this->render('CPCompetitionBundle:BinaryTree:tree.html.twig',array("bracketJSON"=>$jstab,"competitionType"=>$competition->getType()));
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

            if($competition->getFatherRound()->getGame()==$game){
                if($game->getScore1()>$game->getScore2()){
                    $winner = $game->getJoueur1();
                }
                else if($game->getScore1()==$game->getScore2()){
                    $winner = false;
                }
                else {
                    $winner = $game->getJoueur2();
                }

                $winner->addTrophy($competition->getName(),1);
                $competition->setState(2);
            }

            else if($competition->getType()=="treeSimple"){
                $tree->game_valid_simple($competition,$emanage,$game);
            }
            else if($competition->getType()=="treeDouble"){
                $tree->game_valid_double($competition,$emanage,$game);
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
