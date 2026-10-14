<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Message\Query;

use App\Application\Dashboard\MessageHandler\Query\GetVacancyStatusDashboardHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetVacancyStatusDashboardHandler */
final class GetVacancyStatusDashboardQuery
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}
