<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Company\DTOFactory;

use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Company\ControllerRequest\Api\GetCollectionRequest;
use App\VacancyGrabber\Application\Company\ControllerRequest\Api\GetSummaryRequest;
use App\VacancyGrabber\Application\Company\DTO\CompanyCriteriaDTO;
use App\VacancyGrabber\Application\Shared\Service\Util;

use function trim;

class CompanyCriteriaDTOFactory
{
    public function fromCollectionRequest(
        ?UserInterface $user,
        GetCollectionRequest $request,
    ): CompanyCriteriaDTO {
        return new CompanyCriteriaDTO(
            userId: $user?->getId(),
            page: $request->page,
            perPage: $request->perPage,
            query: trim($request->query ?: '') ?: null,
            orderBy: $request->orderBy,
            orderDirection: $request->orderDirection,
        );
    }

    public function fromSummaryRequest(
        ?UserInterface $user,
        GetSummaryRequest $request,
    ): CompanyCriteriaDTO {
        return new CompanyCriteriaDTO(
            userId: $user?->getId(),
            withNoteOnly: Util::normalizeBool($request->withNoteOnly),
            withVacancyOnly: Util::normalizeBool($request->withVacancyOnly),
        );
    }
}
