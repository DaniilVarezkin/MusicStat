<?php

namespace App\Dto;

use App\Entity\Album;
use App\Entity\Artist;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CreateAlbumDto
{
    function __construct(
        #[Assert\NotBlank(message: 'Пожалуйста, укажите название')]
        public ?string            $title = null,

        #[Assert\NotBlank(message: 'Пожалуйста, укажите оценку')]
        #[Assert\Range(
            notInRangeMessage: 'Оценка должна быть от {{ min }} до {{ max }}',
            min: 1,
            max: 100
        )]
        public ?int               $criticScore = null,

        #[Assert\NotBlank(message: 'Пожалуйста, укажите дату релиза')]
        public ?DateTimeImmutable $releaseDate = null,

        #[Assert\NotBlank(message: 'Пожалуйста, укажите хотя бы одного автора')]
        #[Assert\Count(min: 1)]
        public Collection $authors = new ArrayCollection(),

        public UploadedFile|null $cover = null,
    )
    {
    }

    public static function fromEntity(Album $album): self
    {
        return new self(
            $album->getTitle(),
            $album->getCriticScore(),
            $album->getReleaseDate(),
            new ArrayCollection($album->getAuthors()->toArray()),
            null
        );
    }
}
