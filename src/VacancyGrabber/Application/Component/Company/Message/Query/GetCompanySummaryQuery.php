<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Company\Message\Query;

use App\VacancyGrabber\Application\Component\Company\DTO\CompanyCriteriaDTO;
use App\VacancyGrabber\Application\Component\Company\MessageHandler\Query\GetCompanySummaryHandler;

/** @see GetCompanySummaryHandler */
final readonly class GetCompanySummaryQuery
{
    public function __construct(
        public CompanyCriteriaDTO $criteria,
    ) {
    }
}
