<?php

declare(strict_types=1);

namespace App\Component\Company\Message\Query;

use Symfony\Component\Uid\Uuid;

/** @see GetCompanySummaryHandler */
final class GetCompanySummaryQuery
{
    public function __construct(
        public ?Uuid $userId,
        public ?bool $withNoteOnly,
        public ?bool $withVacancyOnly,
    ) {
    }
}
