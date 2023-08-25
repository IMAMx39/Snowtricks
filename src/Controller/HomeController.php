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
        $imagesUrl = $this->getParameter('tricks_images_uri');
        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'imagesUrl' => $imagesUrl,
            'offset' => min(count($tricks), $offset + TrickRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/tricks/loadmore', name: 'app_loadmore_ticks', methods: ['GET'])]
    public function loadmore(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $tricks = $this->trickRepository->getTricksPaginator($offset);
        $imagesUrl = $this->getParameter('tricks_images_uri');

        return $this->render('home/_loadmoreTricks.html.twig', [
            'tricks' => $tricks,
            'offset' => min(count($tricks), $offset + TrickRepository::PAGINATOR_PER_PAGE),
            'imagesUrl' => $imagesUrl
        ]);

    }


}
