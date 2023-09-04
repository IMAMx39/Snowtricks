<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickFormType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\ManagerFile;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickController extends AbstractController
{
    public function __construct(
        private readonly TrickRepository        $trickRepository,
        private readonly CommentRepository      $commentRepository,
        private readonly ManagerFile            $fileManager,
        private readonly EntityManagerInterface $entityManager,
    )
    {
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
            $existing = $this->trickRepository->findBy(['slug' => $slug]);
            if (!empty($existing)) {
                $form->addError(new FormError("Ce titre est indisponible"));
                return $this->render('trick/new.html.twig', [
                    'formTrick' => $form->createView()
                ]);
            }

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
        $avatar = $this->getParameter('avatars_uri');

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $paginator,
            'imagesUrl' => $imagesUri,
            'avatar' => $avatar,
            'offset' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'form' => $form->createView()
        ]);
    }

    #[Route('/trick/{slug}/comments', name: 'app_trick_comment', methods: ['GET'])]
    public function commentsTrick(Trick $trick, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->commentRepository->getCommentPaginator($trick, $offset);
        $avatar = $this->getParameter('avatars_uri');


        return $this->render('trick/_comments.html.twig', [
            'comments' => $paginator,
            'offset' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'trick' => $trick,
            'avatar' => $avatar,
        ]);
    }

    #[Route('/trick/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick): Response
    {
        $picturesUri = $this->getParameter('tricks_images_uri');
        $editTrickForm = $this->createForm(TrickFormType::class, $trick);
        $trickTitle = $trick->getName();
        $editTrickForm->handleRequest($request);
        if ($editTrickForm->isSubmitted() && $editTrickForm->isValid()) {
            $slug = (new AsciiSlugger())->slug($trick->getName());
            $oldSlug = $trick->getSlug();
            $trick->setSlug($slug);
            $trick->setUpdatedAt(new DateTimeImmutable());
            $this->fileManager->renameTrickPicsDir($oldSlug, $slug);
            $this->handleImages($editTrickForm->get('images')->getData(), $slug, $trick);

            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Le trick "' . $trickTitle . '" a bien été modifié'
            );
            return $this->redirectToRoute('app_trick', ['slug' => $slug]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'picturesUri' => $picturesUri,
            'formTrick' => $editTrickForm->createView(),
        ]);

    }

    #[Route('/trick/{slug}/delete', name: 'app_trick_delete')]
    public function delete(Trick $trick): Response
    {
        $this->trickRepository->remove($trick, true);
        $this->fileManager->removeTrickPicsDir($trick->getSlug());
        $this->addFlash('success', 'Le trick "' . $trick->getName() . '" a bien été supprimé');

        return $this->redirectToRoute('app_home');
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
