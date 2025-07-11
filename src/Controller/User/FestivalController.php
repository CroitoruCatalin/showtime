<?php

namespace App\Controller\User;

use App\Entity\Booking;
use App\Entity\Festival;
use App\Form\FestivalBookingType;
use App\Repository\ScheduleSlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/festival')]
class FestivalController extends AbstractController
{
    #[Route('/{id}', name: 'user_festival_show', methods: ['GET'])]
    public function show(
        Festival               $festival,
        ScheduleSlotRepository $slotRepo,
    ): Response
    {
        $slots = $slotRepo->findBy(
            ['festival' => $festival],
            ['startTime' => 'ASC']
        );
        return $this->render('festival/show.html.twig', [
            'festival' => $festival,
            'slots' => $slots,
        ]);
    }

    #[Route('/{id}/book', name: 'user_festival_book', methods: ['GET', 'POST'])]
    public function new(
        Festival               $festival,
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $booking = new Booking();
        $booking->setFestival($festival);
        $form = $this->createForm(FestivalBookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setFestival($festival);
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully created booking.');
            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

}
