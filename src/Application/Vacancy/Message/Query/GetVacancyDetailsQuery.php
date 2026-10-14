<?php

declare(strict_types=1);

namespace App\Application\Vacancy\Message\Query;

use App\Application\Vacancy\MessageHandler\Query\GetVacancyDetailsHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetVacancyDetailsHandler */
final readonly class GetVacancyDetailsQuery
{
    public function __construct(
        public ?Uuid $userId,
        public Uuid $vacancyId,
    ) {
    }
}
