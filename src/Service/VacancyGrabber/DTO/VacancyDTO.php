<?php

declare(strict_types=1);

namespace App\Service\VacancyGrabber\DTO;

readonly class VacancyDTO
{
    public function __construct(
        public ?string $externalId,
        public ?string $url,
        public ?string $title,
        public ?string $description,
        public ?string $salary,
        public ?CompanyDTO $company,
    ) {
    }
}
