<?php

declare(strict_types=1);

namespace App\Component\Source\Message\Query;

use App\Component\Source\MessageHandler\Query\GetSourceDetailsHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetSourceDetailsHandler */
final readonly class GetSourceDetailsQuery
{
    public function __construct(
        public Uuid $id,
    ) {
    }
}
