<?php

namespace App\Common\Trait;
use Doctrine\ORM\Mapping as ORM;

trait HasPhotoUrlTrait
{
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $photoUrl;

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): static
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }
}
