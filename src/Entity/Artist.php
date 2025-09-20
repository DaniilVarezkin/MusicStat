<?php

namespace App\Entity;

use App\Common\Trait\HasPhotoUrlTrait;
use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    use HasPhotoUrlTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    /**
     * @var Collection<int, Album>
     */
    #[ORM\ManyToMany(targetEntity: Album::class, mappedBy: 'authors')]
    private Collection $albums;


    public function __construct()
    {
        $this->albums = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * @return Collection<int, Album>
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(Album $album): static
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->addAuthor($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): static
    {
        if ($this->albums->removeElement($album)) {
            $album->removeAuthor($this);
        }

        return $this;
    }

//    /**
//     * @return Collection<int, Song>
//     */
//    public function getSongs(): Collection
//    {
//        return $this->songs;
//    }
//
//    public function addSong(Song $song): static
//    {
//        if (!$this->songs->contains($song)) {
//            $this->songs->add($song);
//            $song->addAuthor($this);
//        }
//
//        return $this;
//    }
//
//    public function removeSong(Song $song): static
//    {
//        if ($this->songs->removeElement($song)) {
//            $song->removeAuthor($this);
//        }
//
//        return $this;
//    }
}
