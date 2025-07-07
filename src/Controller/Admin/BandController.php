<?php

namespace App\Controller\Admin;

use App\Entity\Band;
use App\Form\BandType;
use App\Repository\BandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/band')]
final class BandController extends AbstractController
{
    #[Route(name: 'admin_band_index', methods: ['GET'])]
    public function index(Request $request, BandRepository $bandRepository): Response
    {
        $qb = $bandRepository->createQueryBuilder('b');
        $letters = $request->query->get('name_starts', '');
        $genre = $request->query->get('genre', '');
        $sort = $request->query->get('sort', 'id');
        $dir = strtoupper($request->query->get('direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';


        if ($letters !== '') {
            $qb->andWhere('b.name LIKE :name_starts')
                ->setParameter('name_starts', $letters . '%');
        }

        if ($genre !== '') {
            $qb->andWhere('b.genre = :genre')
                ->setParameter('genre', $genre);
        }

        if (in_array($sort, ['id', 'name', 'genre'], true)) {
            $qb->orderBy("b.$sort", $dir);
        }

        $bands = $qb->getQuery()->getResult();

        return $this->render('band/index.html.twig',
            [
                'bands' => $bands,
                'letters' => $letters,
                'genre' => $genre,
                'sort' => $sort,
                'dir' => $dir,
                'query' => $request->query->all(),
            ]);
    }

    #[Route('/new', name: 'admin_band_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $band = new Band();
        $form = $this->createForm(BandType::class, $band);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function edit(Request $request, Band $band, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BandType::class, $band);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_band_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('band/edit.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_band_delete', methods: ['POST'])]
    public function delete(Request $request, Band $band, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $band->getId(), $request->request->get('_token'))) {
            $entityManager->remove($band);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_band_index', [], Response::HTTP_SEE_OTHER);
    }
}
