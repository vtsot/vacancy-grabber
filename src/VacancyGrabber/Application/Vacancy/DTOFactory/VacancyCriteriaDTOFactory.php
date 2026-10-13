<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\DTOFactory;

use App\Component\General\Service\Util;
use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Vacancy\ControllerRequest\Api\GetCollectionRequest;
use App\VacancyGrabber\Application\Vacancy\DTO\VacancyCriteriaDTO;
use App\VacancyGrabber\Application\Vacancy\Message\Query\GetVacancyCollectionQuery;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;

use function array_values;
use function trim;

class VacancyCriteriaDTOFactory
{
    public function fromCollectionRequest(
        ?UserInterface $user,
        GetCollectionRequest $request,
    ): VacancyCriteriaDTO {
        return new VacancyCriteriaDTO(
            userId: $user?->getId(),
            page: $request->page,
            perPage: $request->perPage,
            query: trim($request->query ?: '') ?: null,
            orderBy: GetVacancyCollectionQuery::SORT_BY[$request->orderBy ?? '']
                ?? array_values(GetVacancyCollectionQuery::SORT_BY)[0],
            orderDirection: $request->orderDirection,
            viewed: Util::normalizeBool($request->viewed),
            startDay: Util::normalizeDate($request->startDay)?->startOfDay(),
            endDay: Util::normalizeDate($request->endDay)?->endOfDay(),
            source: $request->source,
            company: $request->company,
            status: $request->status ? VacancyStatus::tryFrom($request->status) : null,
            isResumeSent: Util::normalizeBool($request->isResumeSent),
            resumeSentAt: Util::normalizeDate($request->resumeSentAt),
            resumeSentAtStartDay: Util::normalizeDate($request->resumeSentStartDay)?->startOfDay(),
            resumeSentAtEndDay: Util::normalizeDate($request->resumeSentEndDay)?->endOfDay(),
            isDeadLine: Util::normalizeBool($request->isDeadLine),
            deadLineAt: Util::normalizeDate($request->deadLineAt),
            deadLineAtStartDay: Util::normalizeDate($request->deadLineStartDay)?->startOfDay(),
            deadLineAtEndDay: Util::normalizeDate($request->deadLineEndDay)?->endOfDay(),
        );
    }
}
