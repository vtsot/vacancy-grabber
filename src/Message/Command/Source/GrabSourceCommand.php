<?php

declare(strict_types=1);

namespace App\Message\Command\Source;

use App\MessageHandler\Command\Source\GrabSourceHandler;
use Symfony\Component\Uid\Uuid;

/** @see GrabSourceHandler */
final readonly class GrabSourceCommand
{
    public function __construct(
        public Uuid $sourceId,
    ) {
    }
}
