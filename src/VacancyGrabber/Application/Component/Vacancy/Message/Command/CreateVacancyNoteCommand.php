<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Vacancy\Message\Command;

use App\VacancyGrabber\Application\Component\Vacancy\MessageHandler\Command\CreateVacancyNoteHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see CreateVacancyNoteHandler */
final readonly class CreateVacancyNoteCommand
{
    public function __construct(
        public ?Uuid $userId,
        public Uuid $vacancyId,
        public ?CarbonImmutable $notedAt = null,
        public ?string $description = null,
    ) {
    }
}
