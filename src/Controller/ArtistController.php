<?php

namespace App\Controller;

use App\Dto\CreateArtistDto;
use App\Entity\Artist;
use App\Form\ArtistType;
use App\Repository\ArtistRepository;
use App\Service\ArtistService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/artist')]
final class ArtistController extends AbstractController
{
    #[Route(name: 'app_artist_index', methods: ['GET'])]
    public function index(
        Request $request,
        ArtistRepository $artistRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search');
        $page = $request->query->getInt('page', 1);

        $query = $artistRepository->findArtistsQuery($search);

        $pagination = $paginator->paginate(
            $query,
            $page,
            10 // исполнителей на страницу
        );

        return $this->render('artist/index.html.twig', [
            'pagination' => $pagination,
            'currentSearch' => $search,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_artist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArtistService $artistService): Response
    {
        $artistDto = new CreateArtistDto();
        $form = $this->createForm(ArtistType::class, $artistDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $artist = $artistService->createArtist($artistDto);


            return $this->redirectToRoute('app_artist_show', ['id' => $artist->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artist/new.html.twig', [
            'artist' => $artistDto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_artist_show', methods: ['GET'])]
    public function show(Artist $artist): Response
    {
        return $this->render('artist/show.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_artist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Artist $artist, ArtistService $artistService): Response
    {
        $form = $this->createForm(ArtistType::class, CreateArtistDto::fromEntity($artist));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $artistService->updateArtist($form->getData(),  $artist);

            return $this->redirectToRoute('app_artist_show', ['id' => $artist->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artist/edit.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_artist_delete', methods: ['POST'])]
    public function delete(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artist->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($artist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_artist_index', [], Response::HTTP_SEE_OTHER);
    }
}
