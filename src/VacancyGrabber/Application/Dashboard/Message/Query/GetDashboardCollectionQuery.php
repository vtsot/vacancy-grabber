<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Dashboard\Message\Query;

use App\VacancyGrabber\Application\Dashboard\MessageHandler\Query\GetDashboardCollectionHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetDashboardCollectionHandler */
final readonly class GetDashboardCollectionQuery
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}
