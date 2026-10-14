<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Service\SourceGrabber\DTO;

readonly class CompanyDTO
{
    public function __construct(
        public ?string $url,
        public ?string $name,
    ) {
    }
}
