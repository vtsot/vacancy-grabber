<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Source\DTOFactory;

use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Component\Source\ControllerRequest\Api\GetCollectionRequest;
use App\VacancyGrabber\Application\Component\Source\DTO\SourceCriteriaDTO;
use App\VacancyGrabber\Application\Component\Source\Message\Query\GetSourceCollectionQuery;
use App\VacancyGrabber\Application\Shared\Util;

use function array_values;
use function trim;

class SourceCriteriaDTOFactory
{
    public function fromCollectionRequest(
        ?UserInterface $user,
        GetCollectionRequest $request,
    ): SourceCriteriaDTO {
        return new SourceCriteriaDTO(
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
