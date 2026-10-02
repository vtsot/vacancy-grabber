<?php

declare(strict_types=1);

namespace App\Component\Source\Message\Query;

use App\Component\General\Message\Query\AbstractGetCollectionQuery;
use App\Component\Source\MessageHandler\Query\GetSourceCollectionHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see GetSourceCollectionHandler */
final class GetSourceCollectionQuery extends AbstractGetCollectionQuery
{
    public const string FILTER_QUERY     = 'query';
    public const string FILTER_ACTIVE    = 'active';
    public const string FILTER_START_DAY = 'start_day';
    public const string FILTER_END_DAY   = 'end_day';
    public const array FILTERS           = [
        self::FILTER_QUERY     => true,
        self::FILTER_ACTIVE    => true,
        self::FILTER_START_DAY => true,
        self::FILTER_END_DAY   => true,
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
        public readonly ?bool $active = null,
        public readonly ?CarbonImmutable $startDay = null,
        public readonly ?CarbonImmutable $endDay = null,
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
