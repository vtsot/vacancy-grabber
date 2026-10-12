<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Note\DTO;

use Symfony\Component\Uid\Uuid;

/** @see NoteCriteriaDTOFactory */
final readonly class NoteCriteriaDTO
{
    public function __construct(
        public ?Uuid $userId = null,
        public ?int $page = null,
        public ?int $perPage = null,
        public ?string $query = null,
        public ?string $orderBy = null,
        public ?string $orderDirection = null,
    ) {
    }
}
