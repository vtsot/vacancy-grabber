<?php

declare(strict_types=1);

namespace App\DTO\HeaderNavigation;

final readonly class HeaderNavigationDTO
{
    public function __construct(
        public ?string $key,
        public ?string $title,
        public ?string $url,
    ) {
    }
}
