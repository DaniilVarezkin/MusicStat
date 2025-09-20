<?php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artist>
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    public function findArtistsQuery(?string $search = null): Query
    {
        $qb = $this->createQueryBuilder('a');

        if ($search) {
            $qb->andWhere('a.name LIKE :search OR a.bio LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->orderBy('a.name', 'ASC')->getQuery();
    }
}
