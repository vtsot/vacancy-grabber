<?php

declare(strict_types=1);

namespace App\Component\Source\Message\Query;

use App\Component\Source\MessageHandler\Query\GetSourceSummaryHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetSourceSummaryHandler */
final readonly class GetSourceSummaryQuery
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}
