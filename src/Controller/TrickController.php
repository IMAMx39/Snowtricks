<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;

class TrickController extends AbstractController
{
    private TrickRepository $trickRepository;
    private CommentRepository $commentRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(TrickRepository $trickRepository, CommentRepository $commentRepository, EntityManagerInterface $entityManager)
    {
        $this->trickRepository = $trickRepository;
        $this->commentRepository = $commentRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/trick/{slug}', name: 'app_trick')]
    public function showTrick(Trick $trick, Request $request): Response
    {
        $comments = $this->commentRepository->findCommentsByTrick($trick);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request); // handle the request before checking if the form is submitted and valid

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new DateTimeImmutable());


            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a été bien envoyer'
            );

            return $this->redirectToRoute('app_trick', ['slug' => $trick->getSlug()]);
        }

        $imagesUri = $this->getParameter('tricks_images_uri');
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'imagesUrl' => $imagesUri,
            'form' => $form->createView()
        ]);
    }
}
