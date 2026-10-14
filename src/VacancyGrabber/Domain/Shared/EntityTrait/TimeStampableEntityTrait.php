<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\EntityTrait;

use Carbon\CarbonImmutable;

trait TimeStampableEntityTrait
{
    protected ?CarbonImmutable $createdAt;

    protected ?CarbonImmutable $updatedAt;

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updatedAt;
    }
}
