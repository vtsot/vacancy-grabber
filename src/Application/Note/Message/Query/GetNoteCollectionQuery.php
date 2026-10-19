<?php

declare(strict_types=1);

namespace App\Application\Note\Message\Query;

use App\Application\Note\MessageHandler\Query\GetNoteCollectionHandler;
use App\Domain\Note\Repository\NoteCriteria;

/** @see GetNoteCollectionHandler */
final readonly class GetNoteCollectionQuery
{
    public const string FILTER_QUERY   = 'query';
    public const string FILTER_COMPANY = 'company';
    public const string FILTER_VACANCY = 'vacancy';

    public const array FILTERS = [
        self::FILTER_QUERY   => true,
        self::FILTER_COMPANY => true,
        self::FILTER_VACANCY => true,
    ];

    public const array SORT_BY = [
        // front-end => query builder field
        'noted_at'   => 'q.noted_at',
        'created_at' => 'q.createdAt',
    ];

    public function __construct(public NoteCriteria $criteria)
    {
    }
}
