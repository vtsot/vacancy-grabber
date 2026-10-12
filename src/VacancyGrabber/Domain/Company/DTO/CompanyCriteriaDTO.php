<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Company\DTO;

use App\VacancyGrabber\Domain\Company\DTOFactory\CompanyCriteriaDTOFactory;
use Symfony\Component\Uid\Uuid;

/** @see CompanyCriteriaDTOFactory */
final readonly class CompanyCriteriaDTO
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
    ) {
    }
}
