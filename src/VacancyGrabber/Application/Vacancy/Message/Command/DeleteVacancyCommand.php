<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\Message\Command;

use App\VacancyGrabber\Application\Vacancy\MessageHandler\Command\DeleteVacancyHandler;
use Symfony\Component\Uid\Uuid;

/** @see DeleteVacancyHandler */
final readonly class DeleteVacancyCommand
{
    public function __construct(
        public ?Uuid $userId,
        public Uuid $vacancyId,
    ) {
    }
}
