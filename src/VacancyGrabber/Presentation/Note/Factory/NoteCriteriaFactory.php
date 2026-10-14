<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Note\Factory;

use App\VacancyGrabber\Application\Note\Message\Query\GetNoteCollectionQuery;
use App\VacancyGrabber\Domain\Note\Repository\NoteCriteria;
use App\VacancyGrabber\Infrastructure\Security\SecurityUser;
use App\VacancyGrabber\Presentation\Note\ControllerRequest\Api\GetCollectionRequest;

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
            companyId: $request->company,
        );
    }

    //    public function fromSummaryRequest(
    //        ?SecurityUser $user,
    //        GetSummaryRequest $request,
    //    ): CompanyCriteria {
    //        return new CompanyCriteria(
    //            userId: $user?->getId(),
    //            withNoteOnly: Util::normalizeBool($request->withNoteOnly),
    //            withVacancyOnly: Util::normalizeBool($request->withVacancyOnly),
    //        );
    //    }
}
