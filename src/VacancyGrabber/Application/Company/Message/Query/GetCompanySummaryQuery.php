<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Company\Message\Query;

use App\VacancyGrabber\Application\Company\DTO\CompanyCriteriaDTO;
use App\VacancyGrabber\Application\Company\MessageHandler\Query\GetCompanySummaryHandler;

/** @see GetCompanySummaryHandler */
final readonly class GetCompanySummaryQuery
{
    public function __construct(
        public CompanyCriteriaDTO $criteria,
    ) {
    }
}
