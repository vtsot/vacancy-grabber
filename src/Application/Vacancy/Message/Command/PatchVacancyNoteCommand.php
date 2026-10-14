<?php

declare(strict_types=1);

namespace App\Application\Vacancy\Message\Command;

use App\Application\Vacancy\MessageHandler\Command\PatchVacancyNoteHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see PatchVacancyNoteHandler */
final readonly class PatchVacancyNoteCommand
{
    public function __construct(
        public ?Uuid $userId,
        public Uuid $vacancyId,
        public Uuid $noteId,
        public array $properties,
        public ?CarbonImmutable $notedAt = null,
        public ?string $description = null,
    ) {
    }
}
