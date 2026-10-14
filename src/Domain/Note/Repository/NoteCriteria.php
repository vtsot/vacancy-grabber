<?php

declare(strict_types=1);

namespace App\Domain\Note\Repository;

use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class NoteCriteria
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
        public ?CarbonImmutable $notedAtStartDay = null,
        public ?CarbonImmutable $notedAtEndDay = null,
        public ?Uuid $vacancyId = null,
        public ?Uuid $companyId = null,
    ) {
    }
}
