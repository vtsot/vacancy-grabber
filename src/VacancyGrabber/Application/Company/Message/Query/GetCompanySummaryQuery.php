<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Company\Message\Query;

use App\VacancyGrabber\Application\Company\MessageHandler\Query\GetCompanySummaryHandler;
use App\VacancyGrabber\Domain\Company\Repository\CompanyCriteria;

/** @see GetCompanySummaryHandler */
final readonly class GetCompanySummaryQuery
{
    public function __construct(
        public CompanyCriteria $criteria,
    ) {
    }
}
