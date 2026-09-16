<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Contract\Dictionary;

enum VacancyStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in-progress';
    case Done       = 'Done';
}
