<?php

declare(strict_types=1);

namespace App\Contract\Dictionary;

enum VacancyStatus: string
{
    case Feedback = 'feedback';
    case InProgress = 'in-progress';
    case Done = 'Done';
}
