<?php

declare(strict_types=1);

namespace App\Domain\Source\Entity;

use App\Domain\GrabLog\Entity\GrabLog;
use App\Domain\Shared\AbstractEntity;
use App\Domain\Shared\ValueObject\Url;
use App\Domain\User\Entity\User;
use App\Domain\Vacancy\Entity\Vacancy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Source extends AbstractEntity
{
    private ?string $name = null;

    private ?Url $url = null;

    private bool $active = true;

    private ?User $user = null;

    private SourceGrabber $grabber = SourceGrabber::Dou;

    /** @var Collection<int, Vacancy> */
    private Collection $vacancies;

    /** @var Collection<int, GrabLog> */
    private Collection $grabLogs;

    public function __construct()
    {
        $this->vacancies = new ArrayCollection();
        $this->grabLogs  = new ArrayCollection();
    }

    public static function create(
        ?User $owner,
        ?string $name,
        ?Url $url,
        bool $active = true,
        SourceGrabber $grabber = SourceGrabber::Dou,
    ): self {
        $source          = new self();
        $source->user    = $owner;
        $source->name    = $name;
        $source->url     = $url;
        $source->active  = $active;
        $source->grabber = $grabber;

        return $source;
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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function changeActive(bool $active): void
    {
        $active ? $this->activate() : $this->deactivate();
    }

    public function getGrabber(): SourceGrabber
    {
        return $this->grabber;
    }

    public function useGrabber(SourceGrabber $grabber): void
    {
        $this->grabber = $grabber;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function assignTo(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getVacancies(): Collection
    {
        return $this->vacancies;
    }

    /**
     * @return Collection<int, GrabLog>
     */
    public function getGrabLogs(): Collection
    {
        return $this->grabLogs;
    }
}
