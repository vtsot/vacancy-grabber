<?php

declare(strict_types=1);

namespace App\Application\Company\DTOFactory;

use App\Application\Company\DTO\CompanySummaryDTO;
use App\Domain\Company\Entity\Company;

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
