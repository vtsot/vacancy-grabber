<?php

declare(strict_types=1);

namespace App\Application\Source\Message\Query;

use App\Application\Source\MessageHandler\Query\GetSourceDetailsHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetSourceDetailsHandler */
final readonly class GetSourceDetailsQuery
{
    public function __construct(
        public Uuid $userId,
        public Uuid $sourceId,
    ) {
    }
}
