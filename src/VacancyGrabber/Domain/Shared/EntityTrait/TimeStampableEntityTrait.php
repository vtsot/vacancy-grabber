<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\EntityTrait;

use Carbon\CarbonImmutable;

trait TimeStampableEntityTrait
{
    protected ?CarbonImmutable $createdAt;

    protected ?CarbonImmutable $updatedAt;

    public function setCreatedAt(CarbonImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(CarbonImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updatedAt;
    }
}
