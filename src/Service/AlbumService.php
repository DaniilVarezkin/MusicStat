<?php

namespace App\Service;

use App\Dto\CreateAlbumDto;
use App\Entity\Album;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AlbumService
{
    function __construct(
        private string $projectDir,
        private AlbumRepository $albumRepository,
        private EntityManagerInterface $em,
    )
    {
    }
    private const ALBUM_PHOTO_DIR =  '/upload/images/album/';
    public function changePhoto(?UploadedFile $photo, int  $albumId): void
    {
        if($photo !== null)
        {
            $photoDirFullPath = $this->projectDir . $this::ALBUM_PHOTO_DIR;
            $fileName = md5(uniqid()).'.'.$photo->guessExtension();
            $photo->move($photoDirFullPath, $fileName);

            $album = $this->albumRepository->find($albumId);
            $album->setPhotoUrl($this::ALBUM_PHOTO_DIR . $fileName);

            $this->em->flush();
        }
    }

    public function createAlbum(CreateAlbumDto $albumDto) : Album
    {
        $album = new Album();
        $album
            ->setTitle($albumDto->title)
            ->setCriticScore($albumDto->criticScore)
            ->setReleaseDate($albumDto->releaseDate);

        foreach ($albumDto->authors as $author){
            $album->addAuthor($author);
        }

        $this->em->persist($album);
        $this->em->flush();

        return $album;
    }

    public function updateAlbum(Album $album ,CreateAlbumDto $albumDto) : Album
    {
        $album
            ->setTitle($albumDto->title)
            ->setCriticScore($albumDto->criticScore)
            ->setReleaseDate($albumDto->releaseDate);

        foreach ($album->getAuthors() as $author){
            $album->removeAuthor($author);
        }

        foreach ($albumDto->authors as $author){
            $album->addAuthor($author);
        }

        $this->em->flush();

        return $album;
    }
}
