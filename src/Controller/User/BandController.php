<?php

namespace App\Controller\User;

use App\Entity\Band;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/band')]
class BandController extends AbstractController
{
    #[Route('/{id}', name: 'user_band_show', methods: ['GET'])]
    public function show(Band $band): Response
    {
        return $this->render('band/user-show.html.twig', [
            'band' => $band,
        ]);
    }
}
