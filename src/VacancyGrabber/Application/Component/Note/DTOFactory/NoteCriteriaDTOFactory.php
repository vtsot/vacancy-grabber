<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Note\DTOFactory;

use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Component\Note\ControllerRequest\Api\GetCollectionRequest;
use App\VacancyGrabber\Application\Component\Note\DTO\NoteCriteriaDTO;
use App\VacancyGrabber\Application\Component\Note\Message\Query\GetNoteCollectionQuery;

use function array_values;
use function trim;

class NoteCriteriaDTOFactory
{
    public function fromCollectionRequest(
        ?UserInterface $user,
        GetCollectionRequest $request,
    ): NoteCriteriaDTO {
        return new NoteCriteriaDTO(
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
    //        ?UserInterface $user,
    //        GetSummaryRequest $request,
    //    ): CompanyCriteriaDTO {
    //        return new CompanyCriteriaDTO(
    //            userId: $user?->getId(),
    //            withNoteOnly: Util::normalizeBool($request->withNoteOnly),
    //            withVacancyOnly: Util::normalizeBool($request->withVacancyOnly),
    //        );
    //    }
}
