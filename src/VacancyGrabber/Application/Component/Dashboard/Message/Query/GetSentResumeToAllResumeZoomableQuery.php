<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Dashboard\Message\Query;

use App\VacancyGrabber\Application\Component\Dashboard\MessageHandler\Query\GetSentResumeToAllResumeZoomableHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetSentResumeToAllResumeZoomableHandler */
final class GetSentResumeToAllResumeZoomableQuery
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}
