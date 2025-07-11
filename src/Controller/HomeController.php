<?php

namespace App\Controller;

use App\Repository\FestivalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_index', methods: ['GET'])]
    public function index(
        FestivalRepository $festivalRepository,
    ): Response
    {
        $festivals = $festivalRepository->findAll();
        return $this->render('home/home.html.twig', [
            'festivals' => $festivals,
        ]);
    }
}
