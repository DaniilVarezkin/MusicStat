<?php

namespace App\Common\Trait;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait HasCreatedAtTrait
{
    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setCreatedAtNow(): static
    {
        $this->createdAt = new DateTimeImmutable();

        return $this;
    }
}
