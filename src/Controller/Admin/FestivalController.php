<?php

namespace App\Controller\Admin;

use App\Entity\Festival;
use App\Form\FestivalType;
use App\Form\SelectBandType;
use App\Repository\BandRepository;
use App\Repository\FestivalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/festival')]
class FestivalController extends AbstractController
{
    #[Route(name: 'admin_festival_index', methods: ['GET'])]
    public function index(FestivalRepository $festivalRepository): Response
    {
        return $this->render('festival/index.html.twig', [
            'festivals' => $festivalRepository->findAll(),
        ]);
    }

    #[Route(
        '/festival/{id}/add-band',
        name: 'admin_festival_add_band',
        methods: ['GET', 'POST']
    )]
    public function addBand(
        BandRepository         $bandRepository,
        FestivalRepository     $festivalRepository,
        int                    $id,
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $festival = $festivalRepository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException('Festival not found');
        }

        $form = $this->createForm(SelectBandType::class, null, [
            'festival' => $festival,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $band = $data['band'];
            $festival->addBand($band);
            $entityManager->flush();
            $this->addFlash('success', $band->getName() . ' added successfully to ' . $festival->getName());
            return $this->redirectToRoute('admin_festival_show', ['id' => $festival->getId()]);
        }


        return $this->render('festival/add-band.html.twig', [
            'festival' => $festival,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/festival/{festival_id}/remove-band/{band_id}',
        name: 'admin_festival_remove_band',
        methods: ['POST'])]
    public function removeBand(
        int                    $festival_id,
        int                    $band_id,
        FestivalRepository     $festivalRepository,
        BandRepository         $bandRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $festival = $festivalRepository->findOneBy(['id' => $festival_id]);
        $band = $bandRepository->findOneBy(['id' => $band_id]);
        if ($festival && $band) {
            $festival->removeBand($band);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_festival_show', ['id' => $festival_id]);
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
    public function show(Festival $festival): Response
    {
        return $this->render('festival/show.html.twig', [
            'festival' => $festival,
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
