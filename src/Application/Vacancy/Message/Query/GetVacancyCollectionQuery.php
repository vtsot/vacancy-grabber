<?php

declare(strict_types=1);

namespace App\Application\Vacancy\Message\Query;

use App\Application\Vacancy\MessageHandler\Query\GetVacancyCollectionHandler;
use App\Domain\Vacancy\Repository\VacancyCriteria;

/** @see GetVacancyCollectionHandler */
final readonly class GetVacancyCollectionQuery
{
    public const string FILTER_QUERY                 = 'query';
    public const string FILTER_START_DAY             = 'start_day';
    public const string FILTER_END_DAY               = 'end_day';
    public const string FILTER_SOURCE                = 'source';
    public const string FILTER_COMPANY               = 'company';
    public const string FILTER_STATUS                = 'status';
    public const string FILTER_VIEWED                = 'viewed';
    public const string FILTER_RESUME_SENT_AT        = 'resume_sent_at';
    public const string FILTER_RESUME_SENT_START_DAY = 'resume_sent_start_day';
    public const string FILTER_RESUME_SENT_END_DAY   = 'resume_sent_end_day';
    public const string FILTER_DEAD_LINE_AT          = 'dead_line_at';
    public const string FILTER_DEAD_LINE_START_DAY   = 'dead_line_start_day';
    public const string FILTER_DEAD_LINE_END_DAY     = 'dead_line_end_day';

    public const array FILTERS = [
        self::FILTER_QUERY                 => true,
        self::FILTER_START_DAY             => true,
        self::FILTER_END_DAY               => true,
        self::FILTER_SOURCE                => true,
        self::FILTER_COMPANY               => true,
        self::FILTER_STATUS                => true,
        self::FILTER_VIEWED                => true,
        self::FILTER_RESUME_SENT_AT        => true,
        self::FILTER_RESUME_SENT_START_DAY => true,
        self::FILTER_RESUME_SENT_END_DAY   => true,
        self::FILTER_DEAD_LINE_AT          => true,
        self::FILTER_DEAD_LINE_START_DAY   => true,
        self::FILTER_DEAD_LINE_END_DAY     => true,
    ];

    public const array SORT_BY = [
        // front-end => query builder field
        'created_at'     => 'q.createdAt',
        'source'         => 'q.source',
        'company'        => 'company.name',
        'status'         => 'q.status',
        'viewed'         => 'q.viewed',
        'resume_sent_at' => 'q.resumeSentAt',
        'dead_line_at'   => 'q.deadLineAt',
    ];

    public function __construct(public VacancyCriteria $criteria)
    {
    }
}
