<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Company\DTOFactory;

// use App\Component\Company\DTO\CompanySummaryDTO;
// use App\Component\Company\Entity\Company;

use App\VacancyGrabber\Domain\Company\DTO\CompanySummaryDTO;
use App\VacancyGrabber\Domain\Company\Entity\Company;

final readonly class CompanySummaryDTOFactory
{
    public function fromEntity(
        Company $entity,
    ): CompanySummaryDTO {
        return new CompanySummaryDTO(
            id: $entity->getId(),
            name: $entity->getName(),
        );
    }
}
