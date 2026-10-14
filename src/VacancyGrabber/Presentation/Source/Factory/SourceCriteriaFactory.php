<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Source\Factory;

use App\VacancyGrabber\Application\Source\Message\Query\GetSourceCollectionQuery;
use App\VacancyGrabber\Domain\Source\Repository\SourceCriteria;
use App\VacancyGrabber\Infrastructure\Security\SecurityUser;
use App\VacancyGrabber\Presentation\Shared\Util;
use App\VacancyGrabber\Presentation\Source\ControllerRequest\Api\GetCollectionRequest;

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
