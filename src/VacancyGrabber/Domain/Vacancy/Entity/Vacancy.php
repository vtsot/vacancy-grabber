<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Vacancy\Entity;

use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Note\Entity\Note;
use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\Shared\EntityTrait\DescriptionEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\ExternalIdEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\SalaryEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\TitleEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\UrlEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\ViewedEntityTrait;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\User\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use function array_filter;
use function implode;

class Vacancy extends AbstractEntity
{
    use TitleEntityTrait;
    use DescriptionEntityTrait;
    use ExternalIdEntityTrait;
    use UrlEntityTrait;
    use SalaryEntityTrait;
    use ViewedEntityTrait;

    private ?User $user = null;

    private ?Source $source = null;

    private ?Company $company = null;

    private ?DateTimeImmutable $resumeSentAt = null;

    private ?DateTimeImmutable $deadLineAt = null;

    private Collection $notes;

    private VacancyStatus $status = VacancyStatus::Pending;

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getTitle() ?? 'New Entity';

        return implode(' ', array_filter($labels));
    }

    public function __construct()
    {
        $this->notes = new ArrayCollection();
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

    public function getResumeSentAt(): ?DateTimeImmutable
    {
        return $this->resumeSentAt;
    }

    public function setResumeSentAt(?DateTimeImmutable $resumeSentAt): static
    {
        $this->resumeSentAt = $resumeSentAt;

        return $this;
    }

    public function getDeadLineAt(): ?DateTimeImmutable
    {
        return $this->deadLineAt;
    }

    public function setDeadLineAt(?DateTimeImmutable $deadLineAt): static
    {
        $this->deadLineAt = $deadLineAt;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setVacancy($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getVacancy() === $this) {
                $note->setVacancy(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?VacancyStatus
    {
        return $this->status;
    }

    public function setStatus(VacancyStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
