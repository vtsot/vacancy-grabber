<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\GrabLog\DTOFactory;

use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\GrabLog\ControllerRequest\Api\GetCollectionRequest;
use App\VacancyGrabber\Application\GrabLog\DTO\GrabLogCriteriaDTO;
use App\VacancyGrabber\Application\GrabLog\Message\Query\GetGrabLogCollectionQuery;
use App\VacancyGrabber\Application\Shared\Service\Util;

use function array_values;
use function trim;

class GrabLogCriteriaDTOFactory
{
    public function fromCollectionRequest(
        ?UserInterface $user,
        GetCollectionRequest $request,
    ): GrabLogCriteriaDTO {
        return new GrabLogCriteriaDTO(
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
