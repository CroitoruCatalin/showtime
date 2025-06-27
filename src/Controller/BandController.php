<?php

namespace App\Controller;

use App\Repository\BandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/band')]
final class BandController extends AbstractController
{
    #[Route('/', name: 'app_band', methods: ['GET'])]
    public function index(BandRepository $bandRepository): Response
    {
        $bands = $bandRepository->findAll();
        return $this->render('band/index.html.twig', [
            'controller_name' => 'BandController',
            'bands' => $bands,
        ]);
    }

    #[Route('/', name: 'app_band_store', methods: ['POST'])]
    public function store(): Response
    {
        return $this->render('band/index.html.twig', [
            'controller_name' => 'BandController',
        ]);
    }
}
