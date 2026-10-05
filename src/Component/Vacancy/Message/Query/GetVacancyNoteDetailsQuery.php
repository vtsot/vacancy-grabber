<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Query;

use App\Component\Vacancy\MessageHandler\Query\GetVacancyNoteDetailsHandler;
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
