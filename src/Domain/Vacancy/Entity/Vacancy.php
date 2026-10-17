<?php

declare(strict_types=1);

namespace App\Domain\Vacancy\Entity;

use App\Domain\Company\Entity\Company;
use App\Domain\Note\Entity\Note;
use App\Domain\Shared\AbstractEntity;
use App\Domain\Shared\Exception\InvalidArgumentException;
use App\Domain\Shared\ValueObject\Url;
use App\Domain\Source\Entity\Source;
use App\Domain\Source\Grabber\GrabbedVacancy;
use App\Domain\User\Entity\User;
use App\Domain\Vacancy\ValueObject\ExternalId;
use App\Domain\Vacancy\ValueObject\Salary;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Vacancy extends AbstractEntity
{
    private ?string $title = null;

    private ?string $description = null;

    private ?ExternalId $externalId = null;

    private ?Url $url = null;

    private ?Salary $salary = null;

    private bool $viewed = false;

    private ?User $user = null;

    private ?Source $source = null;

    private ?Company $company = null;

    private ?DateTimeImmutable $resumeSentAt = null;

    private ?DateTimeImmutable $deadLineAt = null;

    /** @var Collection<int, Note> */
    private Collection $notes;

    private VacancyStatus $status = VacancyStatus::Pending;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public static function create(
        ?User $owner,
        ?string $title,
        ?string $description = null,
        ?ExternalId $externalId = null,
        ?Url $url = null,
        ?Salary $salary = null,
        ?Source $source = null,
        ?Company $company = null,
    ): self {
        $vacancy              = new self();
        $vacancy->user        = $owner;
        $vacancy->title       = $title;
        $vacancy->description = $description;
        $vacancy->externalId  = $externalId;
        $vacancy->url         = $url;
        $vacancy->salary      = $salary;
        $vacancy->source      = $source;
        $vacancy->company     = $company;

        return $vacancy;
    }

    public static function grabbed(?User $owner, Source $source, ?Company $company, GrabbedVacancy $grabbed): self
    {
        return self::create(
            owner: $owner,
            title: $grabbed->title,
            description: $grabbed->description,
            externalId: self::orNull(static fn () => ExternalId::fromNullable($grabbed->externalId)),
            url: self::orNull(static fn () => Url::fromNullable($grabbed->url)),
            salary: self::orNull(static fn () => Salary::fromNullable($grabbed->salary)),
            source: $source,
            company: $company,
        );
    }

    public function __toString(): string
    {
        return $this->title ?? 'New Entity';
    }

    private static function sameMoment(?DateTimeImmutable $a, ?DateTimeImmutable $b): bool
    {
        return $a?->format('U.u') === $b?->format('U.u');
    }

    /**
     * Scraped values are external input: a malformed one is dropped instead of failing the whole import.
     *
     * @template T
     *
     * @param callable(): ?T $factory
     *
     * @return ?T
     */
    private static function orNull(callable $factory): mixed
    {
        try {
            return $factory();
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function rename(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function describe(?string $description): void
    {
        $this->description = $description;
    }

    public function getExternalId(): ?ExternalId
    {
        return $this->externalId;
    }

    public function changeExternalId(?ExternalId $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getUrl(): ?Url
    {
        return $this->url;
    }

    public function changeUrl(?Url $url): void
    {
        $this->url = $url;
    }

    public function getSalary(): ?Salary
    {
        return $this->salary;
    }

    public function changeSalary(?Salary $salary): void
    {
        $this->salary = $salary;
    }

    public function isViewed(): bool
    {
        return $this->viewed;
    }

    public function markViewed(): void
    {
        $this->viewed = true;
    }

    public function markUnviewed(): void
    {
        $this->viewed = false;
    }

    public function changeViewed(bool $viewed): void
    {
        $viewed ? $this->markViewed() : $this->markUnviewed();
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

    public function moveToSource(?Source $source): void
    {
        $this->source = $source;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function assignCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getResumeSentAt(): ?DateTimeImmutable
    {
        return $this->resumeSentAt;
    }

    /**
     * Recording a new resume date means the vacancy has been looked at; null clears the date.
     */
    public function markResumeSent(?DateTimeImmutable $sentAt): void
    {
        if (self::sameMoment($sentAt, $this->resumeSentAt)) {
            return;
        }

        $this->resumeSentAt = $sentAt;

        if ($sentAt !== null) {
            $this->markViewed();
        }
    }

    public function getDeadLineAt(): ?DateTimeImmutable
    {
        return $this->deadLineAt;
    }

    /**
     * Setting a new deadline means the vacancy has been looked at; null clears the deadline.
     */
    public function scheduleDeadline(?DateTimeImmutable $deadLineAt): void
    {
        if (self::sameMoment($deadLineAt, $this->deadLineAt)) {
            return;
        }

        $this->deadLineAt = $deadLineAt;

        if ($deadLineAt !== null) {
            $this->markViewed();
        }
    }

    public function getStatus(): VacancyStatus
    {
        return $this->status;
    }

    public function changeStatus(VacancyStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    /**
     * Adds a note to this vacancy; writing a note means the vacancy has been looked at.
     */
    public function writeNote(?User $author, string $description, DateTimeImmutable $notedAt): Note
    {
        $note = Note::forVacancy($author, $this, $description, $notedAt);

        $this->attachNote($note);

        return $note;
    }

    public function attachNote(Note $note): void
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $this->markViewed();
        }

        $note->attachToVacancy($this);
    }

    public function detachNote(Note $note): void
    {
        if ($this->notes->removeElement($note) && $note->getVacancy() === $this) {
            $note->attachToVacancy(null);
        }
    }

    /**
     * Adder for Sonata's ObjectManipulator, which writes the collection with its own property accessor.
     */
    public function addNote(Note $note): void
    {
        $this->attachNote($note);
    }

    /**
     * Remover for Sonata's ObjectManipulator, see addNote().
     */
    public function removeNote(Note $note): void
    {
        $this->detachNote($note);
    }
}
