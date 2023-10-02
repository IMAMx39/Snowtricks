<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private readonly TrickRepository $trickRepository)
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $tricks = $this->trickRepository->getTricksPaginator($offset);
        $tricksAll = $this->trickRepository->findAll();
        $imagesUrl = $this->getParameter('tricks_images_uri');
        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'allTricks' => $tricksAll,
            'imagesUrl' => $imagesUrl,
            'offset' => min(count($tricks), $offset + TrickRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/tricks/load_more', name: 'app_load_more_ticks', methods: ['GET'])]
    public function loadMore(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $tricks = $this->trickRepository->getTricksPaginator($offset);
        $imagesUrl = $this->getParameter('tricks_images_uri');
        $tricksAll = $this->trickRepository->findAll(count($tricks));

        return $this->render('home/_load_more_tricks.html.twig', [
            'tricks' => $tricks,
            'allTricks' => $tricksAll,
            'offset' => min(count($tricks), $offset + TrickRepository::PAGINATOR_PER_PAGE),
            'imagesUrl' => $imagesUrl
        ]);

    }


}
