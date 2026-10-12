<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Company\Entity;

use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\Shared\EntityTrait\NameEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\UrlEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use function array_filter;
use function implode;

class Company extends AbstractEntity
{
    use NameEntityTrait;
    use UrlEntityTrait;

    private Collection $notes;

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getName() ?? 'New Entity';

        return implode(' ', array_filter($labels));
    }

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }
}
