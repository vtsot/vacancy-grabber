<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\Message\Command;

use App\VacancyGrabber\Application\Vacancy\MessageHandler\Command\UpdateVacancyNoteHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see UpdateVacancyNoteHandler */
final readonly class UpdateVacancyNoteCommand
{
    public function __construct(
        public ?Uuid $userId,
        public Uuid $vacancyId,
        public Uuid $noteId,
        public ?CarbonImmutable $notedAt = null,
        public ?string $description = null,
    ) {
    }
}
