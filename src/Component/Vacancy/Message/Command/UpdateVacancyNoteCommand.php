<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Command;

use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see UpdateVacancyNoteHandler */
final readonly class UpdateVacancyNoteCommand
{
    public function __construct(
        public Uuid $vacancyId,
        public Uuid $noteId,
        public ?CarbonImmutable $notedAt = null,
        public ?string $description = null,
    ) {
    }
}
