<?php

declare(strict_types=1);

namespace App\Application\Vacancy\Message\Query;

use App\Application\Vacancy\MessageHandler\Query\GetVacancyNoteCollectionHandler;
use App\Domain\Note\Repository\NoteCriteria;

/** @see GetVacancyNoteCollectionHandler */
final readonly class GetVacancyNoteCollectionQuery
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
        public NoteCriteria $criteria,
    ) {
    }
}
