<?php

namespace App\Dto;

use App\Entity\Album;
use App\Entity\Artist;
use App\Enum\GenreType;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CreateArtistDto
{
    function __construct(
        #[Assert\NotBlank(message: 'Пожалуйста, укажите название')]
        public ?string $name = null,

        public ?string $bio = null,

        #[Assert\Image(
            maxSize: '5M',
            mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
            mimeTypesMessage: 'Пожалуйста, загрузите изображение в формате JPEG, PNG или WebP'
        )]
        public ?UploadedFile $cover = null,
    )
    {
    }

    public static function fromEntity(Artist $artist): self
    {
        return new self(
            $artist->getName(),
            $artist->getBio(),
        );
    }
}
