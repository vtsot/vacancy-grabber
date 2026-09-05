<?php

declare(strict_types=1);

namespace App\Service\VacancyGrabber;

readonly class CompanyDTO
{
    public function __construct(
        public ?string $url,
        public ?string $name,
    ) {
    }
}
