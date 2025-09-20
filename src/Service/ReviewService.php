<?php

namespace App\Service;

use App\Dto\CreateReviewDto;
use App\Entity\Album;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class ReviewService
{
    function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    )
    {
    }

    public function createReview(User $author, Album $album, CreateReviewDto $dto): Review
    {
        $review = new Review();
        $review
            ->setAuthor($author)
            ->setAlbum($album)
            ->setScore($dto->score)
            ->setText($dto->text)
            ->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($review);
        $this->em->flush();

        $this->recalculateAlbumScore($album);

        return $review;
    }

    public function updateReview(Review $review, Album $album, CreateReviewDto $dto): void
    {
        $review
            ->setScore($dto->score)
            ->setText($dto->text);

        $this->em->flush();

        $this->recalculateAlbumScore($album);
    }

    public function deleteReview(Review $review): void
    {
        if($this->security->isGranted('ROLE_ADMIN')
            || $this->security->getUser()->getId() === $review->getAuthor()->getId()) {

            $album = $review->getAlbum();
            $this->em->remove($review);
            $this->em->flush();

            $this->recalculateAlbumScore($album);
        }
    }

    public function recalculateAlbumScore(Album $album): void
    {
        /** @var ReviewRepository $reviewRepository */
        $reviewRepository = $this->em->getRepository(Review::class);

        $averageScore = $reviewRepository->createQueryBuilder('r')
            ->select('AVG(r.score)')
            ->where('r.album = :album')
            ->setParameter('album', $album)
            ->getQuery()
            ->getSingleScalarResult();

        $album->setUserScore($averageScore === null ? 0 : round((float)$averageScore));
        $this->em->flush();
    }
}
