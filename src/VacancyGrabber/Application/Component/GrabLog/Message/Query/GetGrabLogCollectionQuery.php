<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\GrabLog\Message\Query;

use App\VacancyGrabber\Application\Component\GrabLog\DTO\GrabLogCriteriaDTO;
use App\VacancyGrabber\Application\Component\GrabLog\MessageHandler\Query\GetGrabLogCollectionHandler;

/** @see GetGrabLogCollectionHandler */
final readonly class GetGrabLogCollectionQuery
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
        // front-end => query builder field
        'created_at'              => 'q.createdAt',
        'counter_company_created' => 'q.counterCompanyCreated',
        'counter_vacancy_created' => 'q.counterVacancyCreated',
    ];

    public function __construct(public GrabLogCriteriaDTO $criteria)
    {
    }
}
