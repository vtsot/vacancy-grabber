<?php

declare(strict_types=1);

namespace App\Component\Note\Entity;

use App\Component\Company\Entity\Company;
use App\Component\General\Shared\AbstractEntity;
use App\Component\General\Shared\EntityTrait\DescriptionEntityTrait;
use App\Component\Note\Repository\NoteRepository;
use App\Component\Vacancy\Entity\Vacancy;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function array_filter;
use function implode;

#[ORM\Table(name: 'notes')]
#[ORM\Index(name: 'fk_company_id', columns: ['company_id'])]
#[ORM\Index(name: 'fk_vacancy_id', columns: ['vacancy_id'])]
#[ORM\Index(name: 'idx_noted_at', columns: ['noted_at'])]
#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note extends AbstractEntity
{
    use DescriptionEntityTrait;

    #[ORM\Column(name: 'noted_at', type: Types::DATETIME_IMMUTABLE, precision: 6, nullable: true)]
    private ?DateTimeImmutable $notedAt;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Company $company = null;

    #[ORM\ManyToOne(targetEntity: Vacancy::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'vacancy_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Vacancy $vacancy = null;

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getId() ? 'Note' : 'New Entity';

        return implode(' ', array_filter($labels));
    }

    public function getNotedAt(): ?DateTimeImmutable
    {
        return $this->notedAt;
    }

    public function setNotedAt(?DateTimeImmutable $notedAt): static
    {
        $this->notedAt = $notedAt;

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
}
