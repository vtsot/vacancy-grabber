<?php

declare(strict_types=1);

namespace App\Component\Source\DTOFactory;

use App\Component\Source\DTO\SourceDetailsDTO;
use App\Component\Source\Entity\Source;

final readonly class SourceDetailsDTOFactory
{
    public function fromEntity(
        Source $entity,
    ): SourceDetailsDTO {
        return new SourceDetailsDTO(
            id: $entity->getId(),
            name: $entity->getName(),
            url: $entity->getUrl(),
            active: $entity->isActive(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
