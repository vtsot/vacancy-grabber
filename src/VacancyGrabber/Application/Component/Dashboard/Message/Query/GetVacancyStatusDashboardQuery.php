<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Dashboard\Message\Query;

use App\VacancyGrabber\Application\Component\Dashboard\MessageHandler\Query\GetVacancyStatusDashboardHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetVacancyStatusDashboardHandler */
final class GetVacancyStatusDashboardQuery
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}
