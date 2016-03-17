<?php

namespace CP\CompetitionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CP\CompetitionBundle\Entity\Competition;

class CreateTournoisController extends Controller
{
    public function newAction(Request $request)
    {
        $competition = new Competition();
        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder('form', $competition);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('name', 'text')
            ->add('description', 'text')
            ->add('news', 'text')
            ->add('type', 'text')
            ->add('dateCreate', 'datetime')
            ->add('state', 'text')
            ->add('nbPlayers', 'integer')
            ->add('Créer', 'submit')
            ->add('type', 'choice', array(
                'choices' => array(
                    'treeSimple' => "Simple Elimination Bracket",
                    'treeDouble' => "Double Elimination Bracket",
                    'roundRobinSimple' => "Round Robin + Simple Elimination Bracket",
                    'roundRobinDouble' => "Round Robin + Double Elimination Bracket",
                    'league' => "League"
                ),
            ));


        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // On fait le lien Requête <-> Formulaire

        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $competition->setCreator($user);
            $em->persist($competition);
            $em->flush();
            return $this->redirect($this->generateUrl('cp_competition_viewTournois'));
        }
        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('CPCompetitionBundle:CreateTournois:new.html.twig', array(
            'form' => $form->createView(),
        ));

    }
}
