<?php

namespace App\Controller\Admin;

use App\Entity\Festival;
use App\Entity\ScheduleSlot;
use App\Form\FestivalType;
use App\Form\ScheduleSlotType;
use App\Repository\FestivalRepository;
use App\Repository\ScheduleSlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/festival')]
class FestivalController extends AbstractController
{
    #[Route(name: 'admin_festival_index', methods: ['GET'])]
    public function index(FestivalRepository $festivalRepository): Response
    {
        return $this->render('festival/admin-index.html.twig', [
            'festivals' => $festivalRepository->findAll(),
        ]);
    }


    //nu schimbati
    //revert + bataie

    #[Route(
        '/{id}/slots/new',
        name: 'admin_festival_add_slot',
        methods: ['GET', 'POST']
    )]
    public function newSlot(
        Festival               $festival,
        Request                $request,
        EntityManagerInterface $em
    ): Response
    {
        $slot = new ScheduleSlot();
        $slot->setFestival($festival);

        $form = $this->createForm(ScheduleSlotType::class, $slot, [
            'festival' => $festival,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $start = $slot->getStartTime();
            $end = $slot->getEndTime();

            $festStart = (clone $festival->getStartDate())->setTime(0, 0, 0);
            $festEnd = (clone $festival->getEndDate())->setTime(23, 59, 59);

            if ($start < $festStart || $end > $festEnd) {
                $form->addError(new FormError('The slot must fall between ' . $festStart->format('Y-m-d H:i') . ' and ' . $festEnd->format('Y-m-d H:i') . '.'));
            }

            foreach ($festival->getScheduleSlots() as $existing) {
                if ($start < $existing->getEndTime() && $end > $existing->getStartTime()) {
                    $form->addError(new FormError(sprintf(
                        'That overlaps %s’s existing slot from %s to %s.',
                        $existing->getBand()->getName(),
                        $existing->getStartTime()->format('H:i'),
                        $existing->getEndTime()->format('H:i')
                    )));
                    break;
                }
            }

            $band = $slot->getBand();
            foreach ($band->getScheduleSlots() as $existing) {
                if ($start < $existing->getEndTime() && $end > $existing->getStartTime()) {
                    $form->addError(new FormError(sprintf(
                        '%s is already booked from %s to %s.',
                        $band->getName(),
                        $existing->getStartTime()->format('Y-m-d H:i'),
                        $existing->getEndTime()->format('Y-m-d H:i')
                    )));
                    break;
                }
            }

            if (count($form->getErrors(true)) === 0) {
                $em->persist($slot);
                $em->flush();
                $this->addFlash('success', 'Time slot successfully added to the schedule.');
                return $this->redirectToRoute('admin_festival_show', ['id' => $festival->getId()]);
            }
        }

        return $this->render('festival/add-slot.html.twig', [
            'festival' => $festival,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/{id}/slot/{slotId}',
        name: 'admin_festival_delete_slot',
        methods: ['POST'])]
    public function deleteSlot(
        Festival               $festival,
        int                    $slotId,
        ScheduleSlotRepository $scheduleSlotRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $slot = $scheduleSlotRepository->find($slotId);
        if ($slot && $slot->getFestival() === $festival) {
            $entityManager->remove($slot);
            $entityManager->flush();
            $this->addFlash('success', 'Time slot successfully removed from the schedule.');
        }
        return $this->redirectToRoute('admin_festival_show', ['id' => $festival->getId()]);
    }

    #[Route('/new', name: 'admin_festival_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $festival = new Festival();
        $form = $this->createForm(FestivalType::class, $festival);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($festival);
            $entityManager->flush();

            return $this->redirectToRoute('admin_festival_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('festival/new.html.twig', [
            'festival' => $festival,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_festival_show', methods: ['GET'])]
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

    #[Route('/{id}/edit', name: 'admin_festival_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Festival $festival, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FestivalType::class, $festival);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_festival_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('festival/edit.html.twig', [
            'festival' => $festival,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'admin_festival_delete', methods: ['POST'])]
    public function delete(Request $request, Festival $festival, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $festival->getId(), $request->request->get('_token'))) {
            $entityManager->remove($festival);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_festival_index', [], Response::HTTP_SEE_OTHER);
    }
}
