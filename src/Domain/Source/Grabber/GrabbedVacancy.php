<?php

declare(strict_types=1);

namespace App\Domain\Source\Grabber;

readonly class GrabbedVacancy
{
    public function __construct(
        public ?string $externalId,
        public ?string $url,
        public ?string $title,
        public ?string $description,
        public ?string $salary,
        public ?GrabbedCompany $company,
    ) {
    }
}
