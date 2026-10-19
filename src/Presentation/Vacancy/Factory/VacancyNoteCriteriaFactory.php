<?php

declare(strict_types=1);

namespace App\Presentation\Vacancy\Factory;

use App\Application\Vacancy\Message\Query\GetVacancyNoteCollectionQuery;
use App\Domain\Note\Repository\NoteCriteria;
use App\Infrastructure\Security\SecurityUser;
use App\Presentation\Shared\Util;
use App\Presentation\Vacancy\ControllerRequest\Api\GetNoteCollectionRequest;
use Symfony\Component\Uid\Uuid;

use function array_values;
use function trim;

class VacancyNoteCriteriaFactory
{
    public function fromCollectionRequest(
        ?SecurityUser $user,
        Uuid $vacancyId,
        GetNoteCollectionRequest $request,
    ): NoteCriteria {
        return new NoteCriteria(
            userId: $user?->getId(),
            page: $request->page,
            perPage: $request->perPage,
            query: trim($request->query ?: '') ?: null,
            orderBy: GetVacancyNoteCollectionQuery::SORT_BY[$request->orderBy ?? '']
                ?? array_values(GetVacancyNoteCollectionQuery::SORT_BY)[0],
            orderDirection: $request->orderDirection,
            startDay: Util::normalizeDate($request->startDay)?->startOfDay(),
            endDay: Util::normalizeDate($request->endDay)?->endOfDay(),
            notedAtStartDay: Util::normalizeDate($request->notedAtStartDay)?->startOfDay(),
            notedAtEndDay: Util::normalizeDate($request->notedAtEndDay)?->endOfDay(),
            vacancyId: $vacancyId,
        );
    }
}
