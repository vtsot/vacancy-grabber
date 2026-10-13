<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\DTO;

use App\VacancyGrabber\Application\Source\DTOFactory\SourceCriteriaDTOFactory;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see SourceCriteriaDTOFactory */
final readonly class SourceCriteriaDTO
{
    public function __construct(
        public ?Uuid $userId = null,
        public ?int $page = null,
        public ?int $perPage = null,
        public ?string $query = null,
        public ?string $orderBy = null,
        public ?string $orderDirection = null,
        public ?bool $active = null,
        public ?CarbonImmutable $startDay = null,
        public ?CarbonImmutable $endDay = null,
    ) {
    }
}
