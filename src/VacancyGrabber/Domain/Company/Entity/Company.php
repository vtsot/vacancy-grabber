<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Company\Entity;

use App\VacancyGrabber\Domain\Note\Entity\Note;
use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\Shared\EntityTrait\NameEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\UrlEntityTrait;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use function array_filter;
use function implode;

class Company extends AbstractEntity
{
    use NameEntityTrait;
    use UrlEntityTrait;

    private Collection $vacancies;

    private Collection $notes;

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getName() ?? 'New Entity';

        return implode(' ', array_filter($labels));
    }

    public function __construct()
    {
        $this->vacancies = new ArrayCollection();
        $this->notes     = new ArrayCollection();
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getVacancies(): Collection
    {
        return $this->vacancies;
    }

    public function addVacancy(Vacancy $vacancy): static
    {
        if (!$this->vacancies->contains($vacancy)) {
            $this->vacancies->add($vacancy);
            $vacancy->setCompany($this);
        }

        return $this;
    }

    public function removeVacancy(Vacancy $vacancy): static
    {
        if ($this->vacancies->removeElement($vacancy)) {
            // set the owning side to null (unless already changed)
            if ($vacancy->getCompany() === $this) {
                $vacancy->setCompany(null);
            }
        }

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
            $note->setCompany($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getCompany() === $this) {
                $note->setCompany(null);
            }
        }

        return $this;
    }
}
