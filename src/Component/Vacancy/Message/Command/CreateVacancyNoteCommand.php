<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Command;

use App\Component\Vacancy\MessageHandler\Command\CreateVacancyNoteHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see CreateVacancyNoteHandler */
final readonly class CreateVacancyNoteCommand
{
    public function __construct(
        public Uuid $vacancyId,
        public Uuid $noteId,
        public ?CarbonImmutable $notedAt = null,
        public ?string $description = null,
    ) {
    }
}
