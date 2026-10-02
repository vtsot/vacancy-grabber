<?php

declare(strict_types=1);

namespace App\Component\Source\Message\Command;

use App\Component\Source\MessageHandler\Command\GrabSourceHandler;
use Symfony\Component\Uid\Uuid;

/** @see GrabSourceHandler */
final readonly class GrabSourceCommand
{
    public function __construct(
        public Uuid $userId,
        public Uuid $sourceId,
    ) {
    }
}
