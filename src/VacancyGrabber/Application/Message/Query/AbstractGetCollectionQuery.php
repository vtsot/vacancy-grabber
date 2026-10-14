<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Message\Query;

abstract class AbstractGetCollectionQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $perPage,
        public readonly ?string $query = null,
        public readonly ?string $orderBy = null,
        public readonly ?string $orderDirection = null,
    ) {
    }
}
