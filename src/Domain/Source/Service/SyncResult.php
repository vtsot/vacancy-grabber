<?php

declare(strict_types=1);

namespace App\Domain\Source\Service;

final readonly class SyncResult
{
    public function __construct(
        public int $vacanciesChecked,
        public int $vacanciesUpdated,
    ) {
    }
}
