<?php

declare(strict_types=1);

namespace App\Component\GrabLog\Message\Query;

use App\Component\General\Message\Query\AbstractGetCollectionQuery;
use App\Component\GrabLog\MessageHandler\Query\GetGrabLogCollectionHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see GetGrabLogCollectionHandler */
final class GetGrabLogCollectionQuery extends AbstractGetCollectionQuery
{
    public const string FILTER_QUERY     = 'query';
    public const string FILTER_START_DAY = 'start_day';
    public const string FILTER_END_DAY   = 'end_day';
    public const array  FILTERS          = [
        self::FILTER_QUERY     => true,
        self::FILTER_START_DAY => true,
        self::FILTER_END_DAY   => true,
    ];

    public const array SORT_BY = [
        'created_at'              => 'createdAt',
        'counter_company_created' => 'counterCompanyCreated',
        'counter_vacancy_created' => 'counterVacancyCreated',
    ];

    public function __construct(
        public readonly ?Uuid $userId,
        int $page,
        int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        public readonly ?CarbonImmutable $startDay = null,
        public readonly ?CarbonImmutable $endDay = null,
        public readonly ?Uuid $source = null,
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
