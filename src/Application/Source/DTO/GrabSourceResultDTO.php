<?php

declare(strict_types=1);

namespace App\Application\Source\DTO;

use App\Application\Source\MessageHandler\Command\GrabSourceHandler;

/** @see GrabSourceHandler */
final readonly class GrabSourceResultDTO
{
    public function __construct(
        public int $vacanciesCreated,
        public int $vacanciesUpdated,
    ) {
    }
}
