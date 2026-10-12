<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Note\Message\Query;

use App\Component\General\Message\Query\AbstractGetCollectionQuery;
use App\VacancyGrabber\Application\Note\MessageHandler\Query\GetNoteCollectionHandler;
use Symfony\Component\Uid\Uuid;

/** @see GetNoteCollectionHandler */
final class GetNoteCollectionQuery extends AbstractGetCollectionQuery
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

    public function __construct(
        public readonly Uuid $userId,
        int $page,
        int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        public readonly ?Uuid $company = null,
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
