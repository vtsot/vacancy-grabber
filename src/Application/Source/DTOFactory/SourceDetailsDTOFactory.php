<?php

declare(strict_types=1);

namespace App\Application\Source\DTOFactory;

use App\Application\Source\DTO\SourceDetailsDTO;
use App\Domain\Source\Entity\Source;

final readonly class SourceDetailsDTOFactory
{
    public function fromEntity(
        Source $entity,
    ): SourceDetailsDTO {
        return new SourceDetailsDTO(
            id: $entity->getId(),
            name: $entity->getName(),
            url: $entity->getUrl()?->toString(),
            active: $entity->isActive(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
