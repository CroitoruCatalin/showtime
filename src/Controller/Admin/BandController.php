<?php

namespace App\Controller\Admin;

use App\Entity\Band;
use App\Form\BandType;
use App\Repository\BandRepository;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/band')]
final class BandController extends AbstractController
{
    #[Route(name: 'admin_band_index', methods: ['GET'])]
    public function index(Request $request, BandRepository $bandRepository): Response
    {
        $criteria = [
            'name_starts' => (string)$request->get('name_starts', ''),
            'genre' => (string)$request->get('genre', ''),
            'sort' => (string)$request->get('sort', 'id'),
            'direction' => (string)$request->get('direction', 'ASC'),
        ];

        $page = max(1, (int)$request->get('page', 1));
        $limit = 100;

        $result = $bandRepository->findFilteredPaginatedSorted($criteria, $page, $limit);

        return $this->render('band/index.html.twig',
            [
                'bands' => $result['items'],
                'letters' => $criteria['name_starts'],
                'genre' => $criteria['genre'],
                'sort' => $criteria['sort'],
                'dir' => $criteria['direction'],
                'query' => $request->query->all(),
                'page' => $page,
                'pages' => ceil($result['total'] / $limit),
            ]);
    }

    #[Route('/new', name: 'admin_band_new', methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
        EntityManagerInterface $entityManager,
        ImageService           $imageService
    ): Response
    {
        $band = new Band();
        $form = $this->createForm(BandType::class, $band);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $image = $imageService->upload($imageFile);
                $band->setImage($image);
            }

            $entityManager->persist($band);
            $entityManager->flush();

            return $this->redirectToRoute('admin_band_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('band/new.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_band_show', methods: ['GET'])]
    public function show(Band $band): Response
    {
        return $this->render('band/show.html.twig', [
            'band' => $band,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_band_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        Band                   $band,
        EntityManagerInterface $entityManager,
        ImageService           $imageService,
    ): Response
    {
        $form = $this->createForm(BandType::class, $band);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                if ($band->getImage() !== null) {
                    $imageService->remove($band->getImage());
                }
                $band->setImage($imageService->upload($imageFile));
                $entityManager->flush();
            }
            $entityManager->flush();

            return $this->redirectToRoute('admin_band_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('band/edit.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_band_delete', methods: ['POST'])]
    public function delete(
        Request                $request,
        Band                   $band,
        EntityManagerInterface $entityManager,
        ImageService           $imageService
    ): Response
    {
        if ($this->isCsrfTokenValid('delete' . $band->getId(), $request->request->get('_token'))) {
            $entityManager->remove($band);
            $imageService->remove($band->getImage());
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_band_index', [], Response::HTTP_SEE_OTHER);
    }
}
