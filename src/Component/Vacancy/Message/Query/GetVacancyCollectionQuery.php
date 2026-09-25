<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Query;

use App\Component\General\Message\Query\AbstractGetCollectionQuery;
use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\MessageHandler\Query\GetVacancyCollectionHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see GetVacancyCollectionHandler */
final class GetVacancyCollectionQuery extends AbstractGetCollectionQuery
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
    ];

    public const array SORT_BY = [
        // front-end => query builder field
        'created_at'     => 'q.createdAt',
        'source'         => 'q.source',
        'company'        => 'company.name',
        'status'         => 'q.status',
        'viewed'         => 'q.viewed',
        'resume_sent_at' => 'q.resumeSentAt',
    ];

    public function __construct(
        int $page,
        int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        public readonly ?bool $viewed = null,
        public readonly ?CarbonImmutable $startDay = null,
        public readonly ?CarbonImmutable $endDay = null,
        public readonly ?Uuid $source = null,
        public readonly ?Uuid $company = null,
        public readonly ?VacancyStatus $status = null,
        public readonly ?bool $isResumeSent = null,
        public readonly ?CarbonImmutable $resumeSentAt = null,
        public readonly ?CarbonImmutable $resumeSentAtStartDay = null,
        public readonly ?CarbonImmutable $resumeSentAtEndDay = null,
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
