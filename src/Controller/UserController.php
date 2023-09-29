<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

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

    #[Route('/profile/user/{id}/block', name: 'app_block_profile_user')]
    public function deleteProfile(User $user): Response
    {
        $user->setBlocked(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->addFlash('success', "L'utilisateur à été bien bloqué");
        return $this->redirectToRoute('app_logout');
    }
}
