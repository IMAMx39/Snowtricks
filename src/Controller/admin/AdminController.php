<?php

namespace App\Controller\admin;

use App\Repository\UserRepository;
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
        $imagesUrl = $this->getParameter('tricks_images_uri');

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'avatar' => $avatar,
            'imagesUrl' => $imagesUrl,
        ]);
    }
}
