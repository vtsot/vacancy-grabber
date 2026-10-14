<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\Message\Query;

use App\VacancyGrabber\Application\Shared\Message\Query\AbstractGetCollectionQuery;
use App\VacancyGrabber\Application\Vacancy\MessageHandler\Query\GetVacancyNoteCollectionHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see GetVacancyNoteCollectionHandler */
final class GetVacancyNoteCollectionQuery extends AbstractGetCollectionQuery
{
    public const string FILTER_QUERY     = 'query';
    public const string FILTER_START_DAY = 'start_day';
    public const string FILTER_END_DAY   = 'end_day';

    public const array FILTERS = [
        self::FILTER_QUERY     => true,
        self::FILTER_START_DAY => true,
        self::FILTER_END_DAY   => true,
    ];

    public const array SORT_BY = [
        // front-end => query builder field
        'noted_at'    => 'q.notedAt',
        'description' => 'q.description',
    ];

    public function __construct(
        public readonly ?Uuid $userId,
        public readonly Uuid $vacancyId,
        int $page,
        int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        public readonly ?CarbonImmutable $startDay = null,
        public readonly ?CarbonImmutable $endDay = null,
        public readonly ?CarbonImmutable $notedAtStartDay = null,
        public readonly ?CarbonImmutable $notedAtEndDay = null,
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
