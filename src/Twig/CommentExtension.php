<?php

namespace App\Twig;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

 class CommentExtension extends AbstractExtension
{

    public function __construct(private readonly CommentRepository $commentRepository)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('user_comments', [$this, 'userComments']),
        ];
    }

    /**
     * @param User $user
     * @return array<Comment>
     */
    public function userComments(User $user): array
    {
        return $this->commentRepository->findBy(['author' => $user]);
    }

}