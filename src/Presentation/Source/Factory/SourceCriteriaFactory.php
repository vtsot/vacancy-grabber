<?php

declare(strict_types=1);

namespace App\Presentation\Source\Factory;

use App\Application\Source\Message\Query\GetSourceCollectionQuery;
use App\Domain\Source\Repository\SourceCriteria;
use App\Infrastructure\Security\SecurityUser;
use App\Presentation\Shared\Util;
use App\Presentation\Source\ControllerRequest\Api\GetCollectionRequest;

use function array_values;
use function trim;

class SourceCriteriaFactory
{
    public function fromCollectionRequest(
        ?SecurityUser $user,
        GetCollectionRequest $request,
    ): SourceCriteria {
        return new SourceCriteria(
            userId: $user?->getId(),
            page: $request->page,
            perPage: $request->perPage,
            query: trim($request->query ?: '') ?: null,
            orderBy: GetSourceCollectionQuery::SORT_BY[$request->orderBy ?? '']
                ?? array_values(GetSourceCollectionQuery::SORT_BY)[0],
            orderDirection: $request->orderDirection,
            active: Util::normalizeBool($request->active),
            startDay: Util::normalizeDate($request->startDay)?->startOfDay(),
            endDay: Util::normalizeDate($request->endDay)?->endOfDay(),
        );
    }
}
