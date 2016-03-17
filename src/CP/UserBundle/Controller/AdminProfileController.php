<?php

namespace CP\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CP\UserBundle\Entity\Game;
use Symfony\Component\HttpFoundation\Request;

class AdminProfileController extends Controller
{
    public function adminViewProfilesAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $listUsers = $userManager->findUsers();
        //$em = $this->getDoctrine()->getManager();

        //$listUsers = $em->getRepository('CPUserBundle:User')->findAll();

        return $this->render('CPUserBundle:AdminUsers:adminViewProfiles.html.twig', array(
            'users' => $listUsers,
        ));
    }

}