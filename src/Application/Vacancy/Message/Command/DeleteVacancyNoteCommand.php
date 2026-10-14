<?php

declare(strict_types=1);

namespace App\Application\Vacancy\Message\Command;

use App\Application\Vacancy\MessageHandler\Command\DeleteVacancyNoteHandler;
use Symfony\Component\Uid\Uuid;

/** @see DeleteVacancyNoteHandler */
final readonly class DeleteVacancyNoteCommand
{
    public function __construct(
        public ?Uuid $userId,
        public Uuid $vacancyId,
        public Uuid $noteId,
    ) {
    }
}
