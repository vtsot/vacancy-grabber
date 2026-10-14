<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Company\Entity;

use App\VacancyGrabber\Domain\Note\Entity\Note;
use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\Shared\ValueObject\Url;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Company extends AbstractEntity
{
    private ?string $name = null;

    private ?Url $url = null;

    /** @var Collection<int, Vacancy> */
    private Collection $vacancies;

    /** @var Collection<int, Note> */
    private Collection $notes;

    public function __construct()
    {
        $this->vacancies = new ArrayCollection();
        $this->notes     = new ArrayCollection();
    }

    public static function create(string $name, ?Url $url = null): self
    {
        $company       = new self();
        $company->name = $name;
        $company->url  = $url;

        return $company;
    }

    public function __toString(): string
    {
        return $this->name ?? 'New Entity';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function rename(?string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): ?Url
    {
        return $this->url;
    }

    public function changeUrl(?Url $url): void
    {
        $this->url = $url;
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getVacancies(): Collection
    {
        return $this->vacancies;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function attachNote(Note $note): void
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
        }

        $note->attachToCompany($this);
    }

    public function detachNote(Note $note): void
    {
        if ($this->notes->removeElement($note) && $note->getCompany() === $this) {
            $note->attachToCompany(null);
        }
    }
}
