<?php

namespace App\Controller;

use App\Dto\CreateAlbumDto;
use App\Dto\CreateReviewDto;
use App\Entity\Album;
use App\Enum\GenreType;
use App\Form\AlbumType;
use App\Form\ReviewForm;
use App\Repository\AlbumRepository;
use App\Repository\ReviewRepository;
use App\Service\AlbumService;
use App\Service\ReviewService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/album')]
final class AlbumController extends AbstractController
{
//    #[Route(name: 'app_album_index', methods: ['GET'])]
//    public function index(AlbumRepository $albumRepository): Response
//    {
//        return $this->render('album/index.html.twig', [
//            'albums' => $albumRepository->findAll(),
//        ]);
//    }

    #[Route('/top', name: 'app_album_index', methods: ['GET'])]
    public function top(
        Request            $request,
        AlbumRepository    $albumRepository,
        PaginatorInterface $paginator
    ): Response
    {
        // Получаем параметры фильтрации
        $year = $request->query->get('year');
        $genre = $request->query->get('genre');
        $search = $request->query->get('search');
        $page = $request->query->getInt('page', 1);

        // Получаем топ альбомов с фильтрацией (без лимита)
        $albumsQuery = $albumRepository->findTopAlbumsQuery(
            year: $year ? (int)$year : null,
            genre: $genre,
            search: $search
        );

        // Пагинируем результаты
        $pagination = $paginator->paginate(
            $albumsQuery,
            $page,
            6 // Альбомов на страницу
        );

        // Получаем список годов для фильтра
        $availableYears = $albumRepository->findAvailableYears();

        return $this->render('album/top.html.twig', [
            'pagination' => $pagination,
            'availableYears' => $availableYears,
            'currentYear' => $year,
            'currentGenre' => $genre,
            'currentSearch' => $search,
            'genres' => GenreType::cases(),
        ]);
    }

    #[Route('/new-releases', name: 'app_album_new_releases', methods: ['GET'])]
    public function newReleases(Request $request, AlbumRepository $albumRepository, PaginatorInterface $paginator): Response
    {
        $year = $request->query->get('year');
        $genre = $request->query->get('genre');
        $search = $request->query->get('search');
        $page = $request->query->getInt('page', 1);

        $albumsQuery = $albumRepository->findNewAlbumsQuery(
            year: $year ? (int)$year : null,
            genre: $genre,
            search: $search
        );

        // Пагинируем результаты
        $pagination = $paginator->paginate(
            $albumsQuery,
            $page,
            6 // Альбомов на страницу
        );

        $availableYears = $albumRepository->findAvailableYears();

        return $this->render('album/new_releases.html.twig', [
            'pagination' => $pagination,
            'availableYears' => $availableYears,
            'currentYear' => $year,
            'currentGenre' => $genre,
            'currentSearch' => $search,
            'genres' => GenreType::cases(),
        ]);
    }

    // AlbumController.php

    #[Route('/by-genres', name: 'app_album_by_genres', methods: ['GET'])]
    public function byGenres(AlbumRepository $albumRepository): Response
    {
        $albumsByGenre = $albumRepository->findAllGroupedByGenres();

        return $this->render('album/by_genres.html.twig', [
            'albumsByGenre' => $albumsByGenre,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_album_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AlbumService $albumService): Response
    {
        $albumDto = new CreateAlbumDto();
        $form = $this->createForm(AlbumType::class, $albumDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $album = $albumService->createAlbum($albumDto);

            return $this->redirectToRoute('app_album_show', ['id' => $album->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('album/new.html.twig', [
//            'album' => $album,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_album_show', methods: ['GET', 'POST'])]
    public function show(
        Request          $request,
        Album            $album,
        ReviewService    $reviewService,
        ReviewRepository $reviewRepository,
    ): Response
    {
        $review = $reviewRepository->findOneBy(['album' => $album, 'author' => $this->getUser()]);
        $createReviewDto = new CreateReviewDto();

        if ($review) {
            $createReviewDto = new CreateReviewDto(
                $review->getScore(),
                $review->getText(),
            );
        }

        $reviewForm = $this->createForm(ReviewForm::class, $createReviewDto);

        $reviewForm->handleRequest($request);

        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
            try {
                /** @var CreateReviewDto $createReviewDto */
                $createReviewDto = $reviewForm->getData();
                if ($review) {
                    $reviewService->updateReview($review, $album, $createReviewDto);
                    $this->addFlash('success', 'Отзыв успешно обновлён!'); // эти нет
                } else {
                    $reviewService->createReview($this->getUser(), $album, $createReviewDto);
                    $this->addFlash('success', 'Отзыв успешно добавлен!'); // эти нет
                }


                return $this->redirectToRoute('app_album_show', ['id' => $album->getId()]);

            } catch (\Exception $e) {
                $this->addFlash('error', 'Произошла ошибка при сохранении отзыва');
            }
        }

        return $this->render('album/show.html.twig', [
            'user_review' => $review,
            'album' => $album,
            'review_form' => $reviewForm,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_album_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Album $album, AlbumService $albumService): Response
    {
        $form = $this->createForm(AlbumType::class, CreateAlbumDto::fromEntity($album));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $albumService->updateAlbum($album, $form->getData());

            return $this->redirectToRoute('app_album_show', ['id' => $album->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('album/edit.html.twig', [
            'album' => $album,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{id}', name: 'app_album_delete', methods: ['POST'])]
    public function delete(Request $request, Album $album, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $album->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($album);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_album_index', [], Response::HTTP_SEE_OTHER);
    }
}
