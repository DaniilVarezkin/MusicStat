<?php

namespace App\Service;

use App\Dto\CreateAlbumDto;
use App\Entity\Album;
use App\Enum\GenreType;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AlbumService
{
    function __construct(
        private string                 $projectDir,
        private EntityManagerInterface $em,
    )
    {
    }

    private const ALBUM_PHOTO_DIR = '/upload/images/album/';


    public function createAlbum(CreateAlbumDto $albumDto): Album
    {

        $album = new Album();
        $album
            ->setTitle($albumDto->title)
            ->setDescription($albumDto->description)
            ->setCriticScore($albumDto->criticScore)
            ->setReleaseDate($albumDto->releaseDate)
            ->setGenres($albumDto->genres);

        foreach ($albumDto->authors as $author) {
            $album->addAuthor($author);
        }

        if ($albumDto->cover !== null) {
            $this->changePhoto($albumDto->cover, $album);
        }

        $this->em->persist($album);
        $this->em->flush();

        return $album;
    }

    public function updateAlbum(Album $album, CreateAlbumDto $albumDto): Album
    {
        $album
            ->setTitle($albumDto->title)
            ->setDescription($albumDto->description)
            ->setCriticScore($albumDto->criticScore)
            ->setReleaseDate($albumDto->releaseDate)
            ->setGenres($albumDto->genres);

        foreach ($album->getAuthors() as $author) {
            $album->removeAuthor($author);
        }

        foreach ($albumDto->authors as $author) {
            $album->addAuthor($author);
        }

        if ($albumDto->cover !== null) {
            $this->changePhoto($albumDto->cover, $album);
        }

        $this->em->flush();

        return $album;
    }

    private function changePhoto(UploadedFile $photo, Album $album): void
    {
        $photoDirFullPath = $this->projectDir . $this::ALBUM_PHOTO_DIR;
        $fileName = md5(uniqid()) . '.' . $photo->guessExtension();
        $photo->move($photoDirFullPath, $fileName);

        $album->setPhotoUrl($this::ALBUM_PHOTO_DIR . $fileName);
    }

}
