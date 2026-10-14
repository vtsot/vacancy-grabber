<?php

declare(strict_types=1);

namespace App\Domain\Source\Service;

final readonly class ImportResult
{
    public function __construct(
        public int $companiesCreated,
        public int $vacanciesCreated,
    ) {
    }
}
