<?php

namespace App\Service;

use App\Dto\CreateArtistDto;
use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArtistService
{
    function __construct(
        private string                 $projectDir,
        private EntityManagerInterface $em,
    )
    {
    }

    private const ARTIST_PHOTO_DIR = '/upload/images/artist/';


    public function createArtist(CreateArtistDto $artistDto): Artist
    {

        $artist = new Artist();
        $artist
            ->setName($artistDto->name)
            ->setBio($artistDto->bio);

        if ($artistDto->cover !== null) {
            $this->changePhoto($artistDto->cover, $artist);
        }

        $this->em->persist($artist);
        $this->em->flush();

        return $artist;
    }

    public function updateArtist(CreateArtistDto $artistDto, Artist $artist): Artist
    {
        $artist
            ->setName($artistDto->name)
            ->setBio($artistDto->bio);

        if ($artistDto->cover !== null) {
            $this->changePhoto($artistDto->cover, $artist);
        }

        $this->em->flush();

        return $artist;
    }

    private function changePhoto(UploadedFile $photo, Artist $artist): void
    {
        $photoDirFullPath = $this->projectDir . $this::ARTIST_PHOTO_DIR;
        $fileName = md5(uniqid()) . '.' . $photo->guessExtension();
        $photo->move($photoDirFullPath, $fileName);

        $artist->setPhotoUrl($this::ARTIST_PHOTO_DIR . $fileName);
    }
}
