<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Shared;

use Carbon\CarbonImmutable;

use function array_combine;
use function array_keys;
use function array_map;
use function array_values;
use function explode;
use function filter_var;
use function sprintf;
use function Symfony\Component\String\s;
use function Symfony\Component\String\u;

use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOLEAN;

final class Util
{
    public static function arrayKeysToSnake(array $data): array
    {
        return array_combine(
            array_map(
                static fn (string $key): string => u($key)->snake()->toString(),
                array_keys($data),
            ),
            array_values($data),
        );
    }

    /**
     * 0: order by q.name
     * 1: desc|asc
     * 2 table name.
     *
     * @return array{
     *      0: string,
     *      1: string | null,
     *      2: string,
     *  }
     */
    public static function normalizeOrderBy(string $mainTable, string $orderBy, ?string $orderDirection): array
    {
        if (str_contains($orderBy, '.')) {
            [$table, $field] = explode('.', $orderBy);
        } else {
            $table = $mainTable;
            $field = $orderBy;
        }

        return [
            sprintf('%s.%s', $table, s($field)->camel()->toString()),
            $orderDirection,
            $table,
        ];
    }

    public static function normalizeDate(?string $date): ?CarbonImmutable
    {
        return $date ? CarbonImmutable::createFromFormat('Y-m-d', $date) : null;
    }

    public static function normalizeBool(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
