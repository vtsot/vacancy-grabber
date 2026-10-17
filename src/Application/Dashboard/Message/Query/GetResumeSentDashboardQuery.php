<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Message\Query;

use App\Application\Dashboard\MessageHandler\Query\GetResumeSentDashboardHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetResumeSentDashboardHandler */
final class GetResumeSentDashboardQuery
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}
