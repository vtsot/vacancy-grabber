<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Note\Entity;

use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\User\Entity\User;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use DateTimeImmutable;

class Note extends AbstractEntity
{
    private ?string $description = null;

    private ?DateTimeImmutable $notedAt = null;

    private ?User $user = null;

    private ?Company $company = null;

    private ?Vacancy $vacancy = null;

    /**
     * Use Vacancy::writeNote() from application code so the vacancy reacts to the note.
     */
    public static function forVacancy(?User $author, Vacancy $vacancy, string $description, DateTimeImmutable $notedAt): self
    {
        $note              = new self();
        $note->user        = $author;
        $note->vacancy     = $vacancy;
        $note->description = $description;
        $note->notedAt     = $notedAt;

        return $note;
    }

    public static function forCompany(?User $author, Company $company, string $description, DateTimeImmutable $notedAt): self
    {
        $note              = new self();
        $note->user        = $author;
        $note->company     = $company;
        $note->description = $description;
        $note->notedAt     = $notedAt;

        return $note;
    }

    public function __toString(): string
    {
        return $this->getId() ? 'Note' : 'New Entity';
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function rewrite(?string $description): void
    {
        $this->description = $description;
    }

    public function getNotedAt(): ?DateTimeImmutable
    {
        return $this->notedAt;
    }

    public function changeNotedAt(?DateTimeImmutable $notedAt): void
    {
        $this->notedAt = $notedAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function assignTo(?User $user): void
    {
        $this->user = $user;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function attachToCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getVacancy(): ?Vacancy
    {
        return $this->vacancy;
    }

    public function attachToVacancy(?Vacancy $vacancy): void
    {
        $this->vacancy = $vacancy;
    }
}
