<?php

declare(strict_types=1);

namespace App\Application\Note\Service;

use App\Domain\Note\Entity\Note;
use App\Domain\Shared\Exception\InvalidArgumentException;
use DateTimeImmutable;

use function array_flip;
use function array_key_exists;
use function uksort;

use const PHP_INT_MAX;

final class NoteFieldWriter
{
    private const array FIELDS = [
        'notedAt',
        'description',
    ];

    /**
     * @param array<string, mixed> $values property => new value; unknown properties are ignored
     */
    public function apply(Note $note, array $values): void
    {
        $order = array_flip(self::FIELDS);
        uksort($values, static fn (string $a, string $b): int => ($order[$a] ?? PHP_INT_MAX) <=> ($order[$b] ?? PHP_INT_MAX));

        foreach ($values as $property => $value) {
            if (!array_key_exists($property, $order)) {
                continue;
            }

            match ($property) {
                'notedAt'     => $note->changeNotedAt($this->date($value)),
                'description' => $note->rewrite($value),
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
