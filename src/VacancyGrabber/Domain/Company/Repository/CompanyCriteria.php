<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Company\Repository;

use Symfony\Component\Uid\Uuid;

final readonly class CompanyCriteria
{
    public function __construct(
        public ?Uuid $userId = null,
        public ?int $page = null,
        public ?int $perPage = null,
        public ?string $query = null,
        public ?string $orderBy = null,
        public ?string $orderDirection = null,
        public ?array $ids = null,
        public ?string $name = null,
        public ?bool $withNoteOnly = null,
        public ?bool $withVacancyOnly = null,
    ) {
    }
}
