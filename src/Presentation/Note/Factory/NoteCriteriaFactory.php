<?php

declare(strict_types=1);

namespace App\Presentation\Note\Factory;

use App\Application\Note\Message\Query\GetNoteCollectionQuery;
use App\Domain\Note\Repository\NoteCriteria;
use App\Infrastructure\Security\SecurityUser;
use App\Presentation\Note\ControllerRequest\Api\GetCollectionRequest;
use App\Presentation\Shared\Util;

use function array_values;
use function trim;

class NoteCriteriaFactory
{
    public function fromCollectionRequest(
        ?SecurityUser $user,
        GetCollectionRequest $request,
    ): NoteCriteria {
        return new NoteCriteria(
            userId: $user?->getId(),
            page: $request->page,
            perPage: $request->perPage,
            query: trim($request->query ?: '') ?: null,
            orderBy: GetNoteCollectionQuery::SORT_BY[$request->orderBy ?? '']
                ?? array_values(GetNoteCollectionQuery::SORT_BY)[0],
            orderDirection: $request->orderDirection,
            startDay: Util::normalizeDate($request->startDay),
            endDay: Util::normalizeDate($request->endDay),
            notedAtStartDay: Util::normalizeDate($request->notedAtStartDay),
            notedAtEndDay: Util::normalizeDate($request->notedAtEndDay),
            vacancyId: $request->vacancy,
            companyId: $request->company,
        );
    }
}
