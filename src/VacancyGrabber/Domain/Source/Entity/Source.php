<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Source\Entity;

use App\VacancyGrabber\Domain\GrabLog\Entity\GrabLog;
use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\Shared\EntityTrait\ActiveEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\NameEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\UrlEntityTrait;
use App\VacancyGrabber\Domain\User\Entity\User;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use function array_filter;
use function implode;

class Source extends AbstractEntity
{
    use NameEntityTrait;
    use UrlEntityTrait;
    use ActiveEntityTrait;

    private ?User $user = null;

    private SourceGrabber $grabber = SourceGrabber::Dou;

    private Collection $vacancies;

    private Collection $grabLogs;

    public function __construct()
    {
        $this->vacancies = new ArrayCollection();
        $this->grabLogs  = new ArrayCollection();
    }

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getName() ?? 'New Entity';

        return implode(' ', array_filter($labels));
    }

    public function getGrabber(): ?SourceGrabber
    {
        return $this->grabber;
    }

    public function setGrabber(SourceGrabber $grabber): static
    {
        $this->grabber = $grabber;

        return $this;
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
            $vacancy->setSource($this);
        }

        return $this;
    }

    public function removeVacancy(Vacancy $vacancy): static
    {
        if ($this->vacancies->removeElement($vacancy)) {
            // set the owning side to null (unless already changed)
            if ($vacancy->getSource() === $this) {
                $vacancy->setSource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GrabLog>
     */
    public function getGrabLogs(): Collection
    {
        return $this->grabLogs;
    }

    public function addGrabLog(GrabLog $grabLog): static
    {
        if (!$this->grabLogs->contains($grabLog)) {
            $this->grabLogs->add($grabLog);
            $grabLog->setSource($this);
        }

        return $this;
    }

    public function removeGrabLog(GrabLog $grabLog): static
    {
        if ($this->grabLogs->removeElement($grabLog)) {
            // set the owning side to null (unless already changed)
            if ($grabLog->getSource() === $this) {
                $grabLog->setSource(null);
            }
        }

        return $this;
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
}
