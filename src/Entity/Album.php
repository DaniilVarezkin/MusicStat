<?php

namespace App\Entity;

use App\Common\Trait\HasPhotoUrlTrait;
use App\Enum\GenreType;
use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    use HasPhotoUrlTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $criticScore = null;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $userScore = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $releaseDate = null;

    /**
     * @var Collection<int, Artist>
     */
    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'albums')]
    private Collection $authors;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'album')]
    private Collection $reviews;


    #[ORM\Column]
    private array $genres = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }


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

    public function getCriticScore(): ?int
    {
        return $this->criticScore;
    }

    public function setCriticScore(int $criticScore): static
    {
        $this->criticScore = $criticScore;

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

    public function getReleaseDate(): ?\DateTimeImmutable
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeImmutable $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Artist $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Artist $author): static
    {
        $this->authors->removeElement($author);

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setAlbum($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getAlbum() === $this) {
                $review->setAlbum(null);
            }
        }

        return $this;
    }

    /**
     * @return array<GenreType>
     */
    public function getGenres(): array
    {
        return array_map(static fn($genre) => GenreType::from($genre),$this->genres);
    }

    /**
     * @param array<GenreType> $genres
     */
    public function setGenres(array $genres): static
    {
        $this->genres = $genres;
        return $this;
    }

    /**
     * Добавляет один жанр
     */
    public function addGenre(GenreType $genre): static
    {
        if (!in_array($genre, $this->genres, true)) {
            $this->genres[] = $genre;
            $this->genres = array_unique($this->genres);
        }

        return $this;
    }

    /**
     * Удаляет жанр
     */
    public function removeGenre(GenreType $genre): static
    {
        $key = array_search($genre, $this->genres, true);
        if ($key !== false) {
            unset($this->genres[$key]);
            $this->genres = array_values($this->genres);
        }

        return $this;
    }

    /**
     * Проверяет, есть ли указанный жанр
     */
    public function hasGenre(GenreType $genre): bool
    {
        return in_array($genre, $this->genres, true);
    }

    /**
     * Возвращает русские названия жанров
     *
     * @return array<string>
     */
    public function getGenreLabels(): array
    {
        $labels = [];
        foreach ($this->genres as $genre) {
            $labels[] = $genre->label();
        }
        return $labels;
    }

    /**
     * Возвращает строку с русскими названиями жанров через запятую
     */
    public function getGenresAsString(): string
    {
        return implode(', ', $this->getGenreLabels());
    }

    /**
     * Возвращает массив строковых значений жанров
     *
     * @return array<string>
     */
    public function getGenresAsStrings(): array
    {
        return array_map(fn(GenreType $genre) => $genre->value, $this->genres);
    }

    /**
     * Устанавливает жанры из массива строк
     *
     * @param array<string> $genreStrings
     */
    public function setGenresFromStrings(array $genreStrings): static
    {
        $this->genres = [];
        foreach ($genreStrings as $genreString) {
            $genre = GenreType::tryFrom($genreString);
            if ($genre !== null) {
                $this->addGenre($genre);
            }
        }
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
