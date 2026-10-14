<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Vacancy\Entity;

use App\VacancyGrabber\Domain\Shared\EnumValuesTrait;

enum VacancyStatus: string
{
    use EnumValuesTrait;

    case Pending    = 'pending';
    case InProgress = 'in-progress';

    case Done = 'done';
}
