<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $criricScore = null;

    #[ORM\Column]
    private ?int $userScore = null;

    #[ORM\Column]
    private ?\DateTime $releaseDate = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCriricScore(): ?int
    {
        return $this->criricScore;
    }

    public function setCriricScore(int $criricScore): static
    {
        $this->criricScore = $criricScore;

        return $this;
    }

    public function getUserScore(): ?int
    {
        return $this->userScore;
    }

    public function setUserScore(int $userScore): static
    {
        $this->userScore = $userScore;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }
}
