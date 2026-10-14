<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Source\Message\Query;

use App\VacancyGrabber\Application\Component\Source\DTO\SourceCriteriaDTO;
use App\VacancyGrabber\Application\Component\Source\MessageHandler\Query\GetSourceCollectionHandler;

/** @see GetSourceCollectionHandler */
final readonly class GetSourceCollectionQuery
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

    public function __construct(public SourceCriteriaDTO $criteria)
    {
    }
}
