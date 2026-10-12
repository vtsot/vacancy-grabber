<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Company\Entity;

use App\VacancyGrabber\Domain\Shared\Entity\AbstractEntity;
use App\VacancyGrabber\Domain\Shared\Entity\Traits\NameEntityTrait;
use App\VacancyGrabber\Domain\Shared\Entity\Traits\UrlEntityTrait;

use function array_filter;
use function implode;

class Company extends AbstractEntity
{
    use NameEntityTrait;
    use UrlEntityTrait;

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getName() ?? 'New Entity';

        return implode(' ', array_filter($labels));
    }
}
