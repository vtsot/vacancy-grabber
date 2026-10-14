<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\EntityTrait;

use DateTimeImmutable;

trait NotedAtEntityTrait
{
    private ?DateTimeImmutable $notedAt = null;

    public function getNotedAt(): ?DateTimeImmutable
    {
        return $this->notedAt;
    }

    public function setNotedAt(?DateTimeImmutable $notedAt): static
    {
        $this->notedAt = $notedAt;

        return $this;
    }
}
