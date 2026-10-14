<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Note\Message\Query;

use App\VacancyGrabber\Application\Component\Note\DTO\NoteCriteriaDTO;
use App\VacancyGrabber\Application\Component\Note\MessageHandler\Query\GetNoteCollectionHandler;

/** @see GetNoteCollectionHandler */
final readonly class GetNoteCollectionQuery
{
    public const string FILTER_QUERY   = 'query';
    public const string FILTER_COMPANY = 'company';

    public const array FILTERS = [
        self::FILTER_QUERY   => true,
        self::FILTER_COMPANY => true,
    ];

    public const array SORT_BY = [
        // front-end => query builder field
        'created_at' => 'q.createdAt',
    ];

    public function __construct(public NoteCriteriaDTO $criteria)
    {
    }
}
