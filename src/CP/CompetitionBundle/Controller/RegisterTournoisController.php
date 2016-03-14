<?php

namespace CP\CompetitionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CP\CompetitionBundle\Entity\Competition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegisterTournoisController extends Controller
{
    public function viewAction(Request $request)
    {
        $user = $this->getUser();
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CPCompetitionBundle:Competition')
        ;
        $listTournois = $repository->findAll();

        return $this->render('CPCompetitionBundle:RegisterTournois:viewTournois.html.twig', array('listTournois' => $listTournois, 'user' => $user));
    }

    public function inscriptionAction($id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $tournois = $em->getRepository('CPCompetitionBundle:Competition')->find($id);
        $tournois->addPlayer($user);
        $em->flush();


        return $this->render('CPCompetitionBundle:RegisterTournois:inscription.html.twig', array(
            'tournoisID' => $id,
            'user' => $user,
            'tournois' => $tournois
            ));
    }
}
