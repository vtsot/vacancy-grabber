<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Query;

use App\Component\Vacancy\MessageHandler\Query\GetVacancyDetailsHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetVacancyDetailsHandler */
final readonly class GetVacancyDetailsQuery
{
    public function __construct(
        public Uuid $id,
    ) {
    }
}
