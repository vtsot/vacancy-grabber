<?php

declare(strict_types=1);

namespace App\Application\Company\Message\Query;

use App\Application\Company\MessageHandler\Query\GetCompanySummaryHandler;
use App\Domain\Company\Repository\CompanyCriteria;

/** @see GetCompanySummaryHandler */
final readonly class GetCompanySummaryQuery
{
    public function __construct(
        public CompanyCriteria $criteria,
    ) {
    }
}
