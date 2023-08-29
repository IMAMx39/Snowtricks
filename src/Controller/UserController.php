<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }
    #[Route('/users', name: 'app_users')]
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $users = $this->userRepository->getUsers($offset);
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'offset' => min(count($users), $offset + UserRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/users/loadmore', name: 'app_loadmore_users', methods: ['GET'])]
    public function loadmore(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $users = $this->userRepository->getUsers($offset);
        $imagesUrl = $this->getParameter('tricks_images_uri');

        return $this->render('user/_loadmore_users.html.twig', [
            'users' => $users,
            'offset' => min(count($users), $offset + UserRepository::PAGINATOR_PER_PAGE),
            'imagesUrl' => $imagesUrl
        ]);

    }

    #[Route('/profile', name: 'app_user_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
