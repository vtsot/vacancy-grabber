<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Vacancy\Factory;

use App\VacancyGrabber\Application\Vacancy\Message\Query\GetVacancyCollectionQuery;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyCriteria;
use App\VacancyGrabber\Infrastructure\Security\SecurityUser;
use App\VacancyGrabber\Presentation\Shared\Util;
use App\VacancyGrabber\Presentation\Vacancy\ControllerRequest\Api\GetCollectionRequest;

use function array_values;
use function trim;

class VacancyCriteriaFactory
{
    public function fromCollectionRequest(
        ?SecurityUser $user,
        GetCollectionRequest $request,
    ): VacancyCriteria {
        return new VacancyCriteria(
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
