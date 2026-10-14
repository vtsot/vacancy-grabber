<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Note\Entity;

use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\Shared\EntityTrait\DescriptionEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\NotedAtEntityTrait;
use App\VacancyGrabber\Domain\User\Entity\User;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;

use function array_filter;
use function implode;

class Note extends AbstractEntity
{
    use DescriptionEntityTrait;
    use NotedAtEntityTrait;

    private ?User $user = null;

    private ?Company $company = null;

    private ?Vacancy $vacancy = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getVacancy(): ?Vacancy
    {
        return $this->vacancy;
    }

    public function setVacancy(?Vacancy $vacancy): static
    {
        $this->vacancy = $vacancy;

        return $this;
    }

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getId() ? 'Note' : 'New Entity';

        return implode(' ', array_filter($labels));
    }
}
