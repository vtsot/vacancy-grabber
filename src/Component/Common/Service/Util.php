<?php

declare(strict_types=1);

namespace App\Component\Common\Service;

use Carbon\CarbonImmutable;

use function array_combine;
use function array_keys;
use function array_map;
use function array_values;
use function explode;
use function sprintf;
use function Symfony\Component\String\s;
use function Symfony\Component\String\u;

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
     * @return array<string>
     */
    public static function normalizeOrderBy(string $mainTable, string $orderBy, ?string $orderDirection): array
    {
        if (str_contains($orderBy, '.')) {
            [$table, $field] = explode('.', $orderBy);
        } else {
            $table = $mainTable;
            $field = $orderBy;
        }

        return [sprintf('%s.%s', $table, s($field)->camel()->toString()), $orderDirection];
    }

    public static function normalizeDate(?string $date): ?CarbonImmutable
    {
        return $date ? CarbonImmutable::createFromFormat('Y-m-d', $date) : null;
    }
}
