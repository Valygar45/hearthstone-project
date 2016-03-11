<?php

namespace CP\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfilController extends Controller
{
    public function profilAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $this->getUser();
        //$user->addTrophy('uttarena', 'premier');
        //$userManager->updateUser($user);

        return $this->render('CPUserBundle:Profil:profil.html.twig', array('user' => $user));
    }
}
