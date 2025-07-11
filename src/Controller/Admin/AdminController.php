<?php

namespace App\Controller\Admin;

use App\Repository\BandRepository;
use App\Repository\BookingRepository;
use App\Repository\FestivalRepository;
use App\Repository\ScheduleSlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route(name: 'admin_index', methods: ['GET'])]
    public function index(
        BookingRepository      $bookingRepo,
        FestivalRepository     $festivalRepo,
        BandRepository         $bandRepo,
        ScheduleSlotRepository $scheduleSlotRepo,
    ): Response
    {
        $bookings = $bookingRepo->findAll();
        $festivals = $festivalRepo->findAll();
        $earnings = 0;
        foreach ($bookings as $booking) {
            $earnings += $booking->getFestival()->getPrice();
        }
        $bands = $bandRepo->findAll();
        $slots = $scheduleSlotRepo->findAll();
        return $this->render('admin/index.html.twig', [
            'bookings' => $bookings,
            'festivals' => $festivals,
            'bands' => $bands,
            'slots' => $slots,
            'earnings' => $earnings,
        ]);
    }
}
