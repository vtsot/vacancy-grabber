<?php

declare(strict_types=1);

namespace App\Component\Company\Message\Query;

use App\Component\General\Message\Query\AbstractGetCollectionQuery;
use Symfony\Component\Uid\Uuid;

/** @see GetCompanyCollectionHandler */
final class GetCompanyCollectionQuery extends AbstractGetCollectionQuery
{
    public const string FILTER_QUERY = 'query';
    public const array FILTERS       = [
        self::FILTER_QUERY => true,
    ];

    public const array SORT_BY = [
        // front-end => query builder field
        'created_at' => 'q.createdAt',
        'active'     => 'q.active',
        'name'       => 'q.name',
        'url'        => 'q.url',
    ];

    public function __construct(
        public readonly Uuid $userId,
        int $page,
        int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
    ) {
        parent::__construct(
            page: $page,
            perPage: $perPage,
            query: $query,
            orderBy: $orderBy,
            orderDirection: $orderDirection,
        );
    }
}
