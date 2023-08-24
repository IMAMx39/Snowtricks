<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TrickFormType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\FileUploader;
use App\Service\ManagerFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickController extends AbstractController
{
    private TrickRepository $trickRepository;
    private CommentRepository $commentRepository;
    private EntityManagerInterface $entityManager;
    private ManagerFile $fileManager;


    public function __construct(
        TrickRepository        $trickRepository,
        CommentRepository      $commentRepository,
        ManagerFile            $fileManager,
        EntityManagerInterface $entityManager,
    )
    {
        $this->trickRepository = $trickRepository;
        $this->commentRepository = $commentRepository;
        $this->entityManager = $entityManager;
        $this->fileManager = $fileManager;
    }

    #[Route('/trick/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function createTrick(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = (new AsciiSlugger())->slug($trick->getName());
            $trick->setSlug($slug);
            $trick->setCreatedAt(new DateTimeImmutable());
            $this->handleImages($form->get('images')->getData(), $slug, $trick);
            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                'Votre trick a été bien enregistré'
            );

            return $this->redirectToRoute('app_trick', ['slug' => $slug]);
        }

        return $this->render('trick/new.html.twig', [
            'formTrick' => $form->createView()
        ]);
    }

    #[Route('/trick/{slug}', name: 'app_trick')]
    public function showTrick(Trick $trick, Request $request): Response
    {
        // pagination
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->commentRepository->getCommentPaginator($trick, $offset);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

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
            'comments' => $paginator,
            'imagesUrl' => $imagesUri,
            'offset' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'form' => $form->createView()
        ]);
    }

    #[Route('/trick/{slug}/comments', name: 'app_trick_comment', methods: ['GET'])]
    public function commentsTrick(Trick $trick,Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->commentRepository->getCommentPaginator($trick, $offset);

        return $this->render('trick/_comments.html.twig', [
            'comments' => $paginator,
            'offset' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'trick' => $trick
        ]);
    }

    /**
     * @throws Exception
     */
    private function handleImages(mixed $images, string $trickSlug, Trick $trick): void
    {
        foreach ($images as $image) {
            $file = $image->getFile();

            if ($file === null) {
                continue;
            }

            $fileName = $this->fileManager->uploadTrickImage($file, $trickSlug);
            $image->setFileName($fileName);
            $trick->addImage($image);
        }
    }
}
