<?php

declare(strict_types=1);

namespace App\Application\Source\DTOFactory;

use App\Application\Source\DTO\SourceInfoDTO;
use App\Domain\Source\Entity\Source;

final readonly class SourceInfoDTOFactory
{
    public function fromEntity(
        Source $entity,
    ): SourceInfoDTO {
        return new SourceInfoDTO(
            id: $entity->getId(),
            name: $entity->getName(),
            url: $entity->getUrl()?->toString(),
            active: $entity->isActive(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
