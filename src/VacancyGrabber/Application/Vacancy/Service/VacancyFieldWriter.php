<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\Service;

use App\VacancyGrabber\Domain\Shared\Exception\InvalidArgumentException;
use App\VacancyGrabber\Domain\Shared\ValueObject\Url;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use App\VacancyGrabber\Domain\Vacancy\ValueObject\ExternalId;
use App\VacancyGrabber\Domain\Vacancy\ValueObject\Salary;
use DateTimeImmutable;

use function array_flip;
use function array_key_exists;
use function uksort;

use const PHP_INT_MAX;

/**
 * Applies partial vacancy updates (PATCH-style) through the entity's intent methods.
 */
final class VacancyFieldWriter
{
    /**
     * Application order: "viewed" goes first so that a resume date or deadline, which marks
     * the vacancy as viewed, wins over an explicit viewed=false in the same request.
     */
    private const array FIELDS = [
        'viewed',
        'title',
        'description',
        'externalId',
        'url',
        'salary',
        'resumeSentAt',
        'deadLineAt',
        'status',
    ];

    /**
     * @param array<string, mixed> $values property => new value; unknown properties are ignored
     */
    public function apply(Vacancy $vacancy, array $values): void
    {
        $order = array_flip(self::FIELDS);
        uksort($values, static fn (string $a, string $b): int => ($order[$a] ?? PHP_INT_MAX) <=> ($order[$b] ?? PHP_INT_MAX));

        foreach ($values as $property => $value) {
            if (!array_key_exists($property, $order)) {
                continue;
            }

            match ($property) {
                'viewed'       => $vacancy->changeViewed((bool) $value),
                'title'        => $vacancy->rename($value),
                'description'  => $vacancy->describe($value),
                'externalId'   => $vacancy->changeExternalId(ExternalId::fromNullable($value)),
                'url'          => $vacancy->changeUrl(Url::fromNullable($value)),
                'salary'       => $vacancy->changeSalary(Salary::fromNullable($value)),
                'resumeSentAt' => $vacancy->markResumeSent($this->date($value)),
                'deadLineAt'   => $vacancy->scheduleDeadline($this->date($value)),
                'status'       => $value === null ? null : $vacancy->changeStatus($value instanceof VacancyStatus ? $value : VacancyStatus::from($value)),
            };
        }
    }

    private function date(mixed $value): ?DateTimeImmutable
    {
        if ($value === null || $value instanceof DateTimeImmutable) {
            return $value;
        }

        throw new InvalidArgumentException('Expected a date.');
    }
}
