<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Contract\Dictionary;

use Cerbero\Enum\Concerns\Enumerates;

enum VacancyStatus: string
{
    use Enumerates;

    case Pending    = 'pending';
    case InProgress = 'in-progress';

    // migrate Done value to lowercase + db migration
    case Done = 'Done';
}
