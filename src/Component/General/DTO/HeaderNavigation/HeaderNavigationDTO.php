<?php

declare(strict_types=1);

namespace App\Component\General\DTO\HeaderNavigation;

readonly class HeaderNavigationDTO
{
    public function __construct(
        public ?string $key,
        public ?string $route,
        public ?string $url,
        public ?string $title,
        public ?bool $active = false,
        /** @var HeaderNavigationDTO[] $items */
        public ?array $items = [],
    ) {
    }
}
