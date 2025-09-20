<?php

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Album>
 */
class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    public function findTopAlbums(int $limit = 10, ?int $year = null, ?string $genre = null, ?string $search = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.userScore', 'DESC')
            ->addOrderBy('a.criticScore', 'DESC')
            ->setMaxResults($limit);

        // Фильтр по году для SQLite
        if ($year) {
            $startDate = new \DateTimeImmutable($year . '-01-01');
            $endDate = new \DateTimeImmutable(($year + 1) . '-01-01');

            $qb->andWhere('a.releaseDate >= :startDate AND a.releaseDate < :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }

        // Фильтр по жанру для SQLite (используем LIKE вместо JSON_CONTAINS)
        if ($genre && $genre !== 'all') {
            $qb->andWhere('a.genres LIKE :genre')
                ->setParameter('genre', '%"' . $genre . '"%');
        }

        // Фильтр по названию
        if ($search) {
            $qb->andWhere('a.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function findAvailableYears(): array
    {
        // Для SQLite извлекаем годы через PHP
        $result = $this->createQueryBuilder('a')
            ->select('a.releaseDate')
            ->where('a.releaseDate IS NOT NULL')
            ->getQuery()
            ->getResult();

        $years = [];
        foreach ($result as $item) {
            if ($item['releaseDate'] instanceof \DateTimeInterface) {
                $years[] = (int)$item['releaseDate']->format('Y');
            }
        }

        return array_unique($years);
    }

    public function findNewAlbumsQuery(?int $year = null, ?string $genre = null, ?string $search = null, int $limit = 20): Query
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.releaseDate', 'DESC')
            ->setMaxResults($limit);

        if ($year) {
            $startDate = new \DateTimeImmutable($year . '-01-01');
            $endDate = new \DateTimeImmutable(($year + 1) . '-01-01');

            $qb->andWhere('a.releaseDate >= :startDate AND a.releaseDate < :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }

        if ($genre && $genre !== 'all') {
            $qb->andWhere('a.genres LIKE :genre')
                ->setParameter('genre', '%"' . $genre . '"%');
        }

        if ($search) {
            $qb->andWhere('a.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery();
    }

    public function findAllGroupedByGenres(): array
    {
        $albums = $this->findAll();
        $grouped = [];

        foreach ($albums as $album) {
            foreach ($album->getGenres() as $genre) {
                $genreKey = $genre->value;
                if (!isset($grouped[$genreKey])) {
                    $grouped[$genreKey] = [
                        'genre' => $genre,
                        'albums' => []
                    ];
                }
                // Проверяем, чтобы альбом не дублировался
                $albumExists = false;
                foreach ($grouped[$genreKey]['albums'] as $existingAlbum) {
                    if ($existingAlbum->getId() === $album->getId()) {
                        $albumExists = true;
                        break;
                    }
                }
                if (!$albumExists) {
                    $grouped[$genreKey]['albums'][] = $album;
                }
            }
        }

        // Сортируем альбомы внутри каждого жанра
        foreach ($grouped as &$genreData) {
            usort($genreData['albums'], function($a, $b) {
                return $a->getTitle() <=> $b->getTitle();
            });
        }

        // Сортируем жанры по алфавиту
        uasort($grouped, function($a, $b) {
            return $a['genre']->label() <=> $b['genre']->label();
        });

        return $grouped;
    }

    public function findTopAlbumsQuery(?int $year = null, ?string $genre = null, ?string $search = null): Query
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.userScore', 'DESC')
            ->addOrderBy('a.criticScore', 'DESC');

        // Фильтр по году для SQLite
        if ($year) {
            $startDate = new \DateTimeImmutable($year . '-01-01');
            $endDate = new \DateTimeImmutable(($year + 1) . '-01-01');

            $qb->andWhere('a.releaseDate >= :startDate AND a.releaseDate < :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }

        // Фильтр по жанру для SQLite
        if ($genre && $genre !== 'all') {
            $qb->andWhere('a.genres LIKE :genre')
                ->setParameter('genre', '%"' . $genre . '"%');
        }

        // Фильтр по названию
        if ($search) {
            $qb->andWhere('a.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery();
    }
}
