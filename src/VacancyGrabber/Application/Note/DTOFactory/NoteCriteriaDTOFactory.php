<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Note\DTOFactory;

use App\Component\General\Service\Util;
use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Company\ControllerRequest\Api\GetCollectionRequest;
use App\VacancyGrabber\Application\Company\ControllerRequest\Api\GetSummaryRequest;
use App\VacancyGrabber\Application\Company\DTO\CompanyCriteriaDTO;
use App\VacancyGrabber\Application\Note\DTO\NoteCriteriaDTO;
use App\VacancyGrabber\Application\Note\Message\Query\GetNoteCollectionQuery;

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
