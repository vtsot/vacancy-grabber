<?php

declare(strict_types=1);

namespace App\Application\Source\DTOFactory;

use App\Application\Source\DTO\SourceSummaryDTO;
use App\Domain\Source\Entity\Source;

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
