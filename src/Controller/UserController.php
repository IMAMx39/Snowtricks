<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route('/profile', name: 'app_user_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        $avatarsUri = $this->getParameter('avatars_uri');

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'avatar' => $avatarsUri
        ]);
    }
}
