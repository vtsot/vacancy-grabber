<?php

declare(strict_types=1);

namespace App\Application\Vacancy\Message\Query;

use App\Application\Vacancy\MessageHandler\Query\GetVacancyNoteDetailsHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetVacancyNoteDetailsHandler */
final readonly class GetVacancyNoteDetailsQuery
{
    public function __construct(
        public ?Uuid $userId,
        public Uuid $vacancyId,
        public Uuid $noteId,
    ) {
    }
}
