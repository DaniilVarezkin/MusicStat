<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Review;
use App\Service\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReviewController extends AbstractController
{
    #[Route('/review/delete/{review}', name: 'review_delete')]
    public function delete(Review $review, ReviewService $reviewService): Response
    {
        $album = $review->getAlbum();
        try {
            $reviewService->deleteReview($review);
            $this->addFlash('success', 'Отзыв успешно удален');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Произошла ошибка при удалении отзыва');
        }

        return $this->redirectToRoute('app_album_show', ['id' => $album->getId()]);
    }
}
