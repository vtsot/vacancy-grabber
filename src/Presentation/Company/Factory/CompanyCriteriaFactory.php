<?php

declare(strict_types=1);

namespace App\Presentation\Company\Factory;

use App\Domain\Company\Repository\CompanyCriteria;
use App\Infrastructure\Security\SecurityUser;
use App\Presentation\Company\ControllerRequest\Api\GetCollectionRequest;
use App\Presentation\Company\ControllerRequest\Api\GetSummaryRequest;
use App\Presentation\Shared\Util;

use function trim;

class CompanyCriteriaFactory
{
    public function fromCollectionRequest(
        ?SecurityUser $user,
        GetCollectionRequest $request,
    ): CompanyCriteria {
        return new CompanyCriteria(
            userId: $user?->getId(),
            page: $request->page,
            perPage: $request->perPage,
            query: trim($request->query ?: '') ?: null,
            orderBy: $request->orderBy,
            orderDirection: $request->orderDirection,
        );
    }

    public function fromSummaryRequest(
        ?SecurityUser $user,
        GetSummaryRequest $request,
    ): CompanyCriteria {
        return new CompanyCriteria(
            userId: $user?->getId(),
            withNoteOnly: Util::normalizeBool($request->withNoteOnly),
            withVacancyOnly: Util::normalizeBool($request->withVacancyOnly),
        );
    }
}
