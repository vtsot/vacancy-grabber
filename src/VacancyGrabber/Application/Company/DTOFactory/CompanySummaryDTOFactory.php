<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Company\DTOFactory;

use App\VacancyGrabber\Application\Company\DTO\CompanySummaryDTO;
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
