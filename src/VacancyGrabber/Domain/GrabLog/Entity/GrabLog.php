<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\GrabLog\Entity;

use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\Shared\EntityTrait\DescriptionEntityTrait;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\User\Entity\User;

use function array_filter;
use function implode;

class GrabLog extends AbstractEntity
{
    use DescriptionEntityTrait;

    private ?User $user = null;

    private ?Source $source = null;

    private int $counterCompanyCreated = 0;

    private int $counterVacancyCreated = 0;

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = 'Grab Log ' . $this->getCreatedAt()->format('d-m-Y-H-i-s') ?? 'New Entity';

        return implode(' ', array_filter($labels));
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getCounterCompanyCreated(): ?int
    {
        return $this->counterCompanyCreated;
    }

    public function setCounterCompanyCreated(int $counterCompanyCreated): static
    {
        $this->counterCompanyCreated = $counterCompanyCreated;

        return $this;
    }

    public function getCounterVacancyCreated(): ?int
    {
        return $this->counterVacancyCreated;
    }

    public function setCounterVacancyCreated(int $counterVacancyCreated): static
    {
        $this->counterVacancyCreated = $counterVacancyCreated;

        return $this;
    }
}
