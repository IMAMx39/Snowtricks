<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker  implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
       if ($user->isBlocked()){
           throw new CustomUserMessageAuthenticationException("Votre compte a été banni par un administrateur.");
       }

        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException("Votre Compte n'est pas activer, veuillez vérifier votre boite mail.");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
    }
}