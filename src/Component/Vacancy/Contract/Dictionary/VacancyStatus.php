<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Contract\Dictionary;

use App\Component\General\Shared\EnumValuesTrait;

enum VacancyStatus: string
{
    use EnumValuesTrait;

    case Pending    = 'pending';
    case InProgress = 'in-progress';

    case Done = 'done';
}
