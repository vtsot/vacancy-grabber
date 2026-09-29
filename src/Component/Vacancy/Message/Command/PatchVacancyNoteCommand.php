<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Command;

use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see PatchVacancyNoteHandler */
final readonly class PatchVacancyNoteCommand
{
    public function __construct(
        public Uuid $vacancyId,
        public Uuid $noteId,
        public array $properties,
        public ?CarbonImmutable $notedAt = null,
        public ?string $description = null,
    ) {
    }
}
