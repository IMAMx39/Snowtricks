<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    public function __construct(
        private readonly UserRepository $userRepository,private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/admin', name: 'app_admin')]
    public function users(): Response
    {
        $users = $this->userRepository->findByRole(['ROLE_USER']);
        $avatar = $this->getParameter('avatars_uri');
        $imagesUrl = $this->getParameter('tricks_images_uri');

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'avatar' => $avatar,
            'imagesUrl' => $imagesUrl,
        ]);
    }

    #[Route('/admin/user/{id}/block', name: 'app_block_user')]
    public function blockUser(User $user): Response
    {
        $user->setBlocked(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/user/{id}/unblock', name: 'app_unblock_user')]
    public function unblockUser(User $user): Response
    {
        $user->setBlocked(false);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_admin');
    }
}
