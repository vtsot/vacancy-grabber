<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\Repository;

use App\VacancyGrabber\Domain\Shared\EntityInterface;

interface FindOneByIdentifier
{
    public function findOneByIdentifier(string $identifier): ?EntityInterface;
}
