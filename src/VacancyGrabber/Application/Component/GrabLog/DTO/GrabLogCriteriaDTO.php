<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\GrabLog\DTO;

use App\VacancyGrabber\Application\Component\GrabLog\DTOFactory\GrabLogCriteriaDTOFactory;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see GrabLogCriteriaDTOFactory */
final readonly class GrabLogCriteriaDTO
{
    public function __construct(
        public ?Uuid $userId = null,
        public ?int $page = null,
        public ?int $perPage = null,
        public ?string $query = null,
        public ?string $orderBy = null,
        public ?string $orderDirection = null,
        public ?CarbonImmutable $startDay = null,
        public ?CarbonImmutable $endDay = null,
        public ?Uuid $source = null,
    ) {
    }
}
