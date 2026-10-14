<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\DTO\Navigation;

readonly class NavigationDTO
{
    public function __construct(
        public ?string $key,
        public ?string $route,
        public ?string $url,
        public ?string $title,
        public ?bool $active = false,
        public ?string $prependIcon = null,
        /** @var NavigationDTO[] $items */
        public ?array $items = [],
    ) {
    }
}
