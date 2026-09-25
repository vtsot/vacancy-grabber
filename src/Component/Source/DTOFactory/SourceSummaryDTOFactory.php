<?php

declare(strict_types=1);

namespace App\Component\Source\DTOFactory;

use App\Component\Source\DTO\SourceSummaryDTO;
use App\Component\Source\Entity\Source;

final readonly class SourceSummaryDTOFactory
{
    public function fromEntity(
        Source $entity,
    ): SourceSummaryDTO {
        return new SourceSummaryDTO(
            id: $entity->getId(),
            name: $entity->getName(),
        );
    }
}
