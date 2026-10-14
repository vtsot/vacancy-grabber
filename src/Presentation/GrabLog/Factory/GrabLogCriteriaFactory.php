<?php

declare(strict_types=1);

namespace App\Presentation\GrabLog\Factory;

use App\Application\GrabLog\Message\Query\GetGrabLogCollectionQuery;
use App\Domain\GrabLog\Repository\GrabLogCriteria;
use App\Infrastructure\Security\SecurityUser;
use App\Presentation\GrabLog\ControllerRequest\Api\GetCollectionRequest;
use App\Presentation\Shared\Util;

use function array_values;
use function trim;

class GrabLogCriteriaFactory
{
    public function fromCollectionRequest(
        ?SecurityUser $user,
        GetCollectionRequest $request,
    ): GrabLogCriteria {
        return new GrabLogCriteria(
            userId: $user?->getId(),
            page: $request->page,
            perPage: $request->perPage,
            query: trim($request->query ?: '') ?: null,
            orderBy: GetGrabLogCollectionQuery::SORT_BY[$request->orderBy ?? '']
                ?? array_values(GetGrabLogCollectionQuery::SORT_BY)[0],
            orderDirection: $request->orderDirection,
            startDay: Util::normalizeDate($request->startDay)?->startOfDay(),
            endDay: Util::normalizeDate($request->endDay)?->endOfDay(),
            source: $request->source,
        );
    }
}
