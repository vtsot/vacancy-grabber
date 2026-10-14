<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared;

use function array_column;
use function array_combine;

trait EnumValuesTrait
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function valueCases(): array
    {
        return array_combine(self::values(), self::cases());
    }
}
