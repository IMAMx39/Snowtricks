<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    public function __construct(
        private readonly UserRepository $userRepository,
    )
    {

    }

    #[Route('/admin', name: 'app_admin')]
    public function users(): Response
    {
        $users = $this->userRepository->findByRole(['ROLE_USER']);
        $avatar = $this->getParameter('avatars_uri');
        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'avatar' => $avatar
        ]);
    }
}
