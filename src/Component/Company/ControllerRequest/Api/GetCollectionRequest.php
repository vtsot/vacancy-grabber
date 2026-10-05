<?php

declare(strict_types=1);

namespace App\Component\Company\ControllerRequest\Api;

use App\Component\General\ControllerRequest\AbstractGetCollectionRequest;

final readonly class GetCollectionRequest extends AbstractGetCollectionRequest
{
    public function __construct(
        ?int $page = self::DEFAULT_PAGE,
        ?int $perPage = self::DEFAULT_PER_PAGE,
        ?string $query = null,
        ?string $sort = 'created_at desc',
    ) {
        parent::__construct($page, $perPage, $query, $sort);
    }
}
