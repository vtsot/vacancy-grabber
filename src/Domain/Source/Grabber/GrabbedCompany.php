<?php

declare(strict_types=1);

namespace App\Domain\Source\Grabber;

readonly class GrabbedCompany
{
    public function __construct(
        public ?string $url,
        public ?string $name,
    ) {
    }
}
