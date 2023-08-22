<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickFormType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\FileUploader;
use App\Service\ManagerFile;
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
    private FileUploader $fileUploader;


    public function __construct(
        TrickRepository        $trickRepository,
        CommentRepository      $commentRepository,
        ManagerFile            $fileManager,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    )
    {
        $this->trickRepository = $trickRepository;
        $this->commentRepository = $commentRepository;
        $this->entityManager = $entityManager;
        $this->fileManager = $fileManager;
        $this->fileUploader = $fileUploader;
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
            $this->handleImages($form->get('images')->getData(), $trick);


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
        $comments = $this->commentRepository->findComment($trick);
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

    /**
     * @throws Exception
     */
    private function handleImages(mixed $images, Trick $trick): void
    {
        foreach ($images as $image) {
            $pic = new Image();
            $file = $image->getFile();

            if ($file === null) {
                continue;
            }
            $fileName = $this->fileUploader->upload($file);
            $pic->setFileName($fileName);
            $trick->addImage($pic);

        }
    }
}
