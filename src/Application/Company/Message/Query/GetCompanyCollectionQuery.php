<?php

declare(strict_types=1);

namespace App\Application\Company\Message\Query;

use App\Application\Company\MessageHandler\Query\GetCompanyCollectionHandler;
use App\Domain\Company\Repository\CompanyCriteria;

/** @see GetCompanyCollectionHandler */
final readonly class GetCompanyCollectionQuery
{
    public const string FILTER_QUERY = 'query';
    public const array FILTERS       = [
        self::FILTER_QUERY => true,
    ];

    public const array SORT_BY = [
        // front-end => query builder field
        'created_at' => 'q.createdAt',
        'active'     => 'q.active',
        'name'       => 'q.name',
        'url'        => 'q.url',
    ];

    public function __construct(public CompanyCriteria $criteria)
    {
    }
}
