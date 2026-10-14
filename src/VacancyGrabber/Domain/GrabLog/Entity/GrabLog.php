<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\GrabLog\Entity;

use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\Shared\Exception\InvalidArgumentException;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\User\Entity\User;

class GrabLog extends AbstractEntity
{
    private ?string $description = null;

    private ?User $user = null;

    private ?Source $source = null;

    private int $counterCompanyCreated = 0;

    private int $counterVacancyCreated = 0;

    public static function record(?User $user, Source $source, int $companiesCreated, int $vacanciesCreated): self
    {
        $log         = new self();
        $log->user   = $user;
        $log->source = $source;
        $log->correctCounters($companiesCreated, $vacanciesCreated);

        return $log;
    }

    public function __toString(): string
    {
        return $this->getCreatedAt() ? 'Grab Log ' . $this->getCreatedAt()->format('d-m-Y-H-i-s') : 'New Entity';
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function annotate(?string $description): void
    {
        $this->description = $description;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function assignTo(?User $user): void
    {
        $this->user = $user;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function attachToSource(?Source $source): void
    {
        $this->source = $source;
    }

    public function getCounterCompanyCreated(): int
    {
        return $this->counterCompanyCreated;
    }

    public function getCounterVacancyCreated(): int
    {
        return $this->counterVacancyCreated;
    }

    public function correctCounters(int $companiesCreated, int $vacanciesCreated): void
    {
        if ($companiesCreated < 0 || $vacanciesCreated < 0) {
            throw new InvalidArgumentException('Grab log counters cannot be negative.');
        }

        $this->counterCompanyCreated = $companiesCreated;
        $this->counterVacancyCreated = $vacanciesCreated;
    }
}
