<?php

declare(strict_types=1);

namespace App\Component\Source\DTOFactory;

use App\Component\Source\DTO\SourceInfoDTO;
use App\Entity\Source;

final readonly class SourceInfoDTOFactory
{
    public function fromEntity(
        Source $entity,
    ): SourceInfoDTO {
        return new SourceInfoDTO(
            id: $entity->getId(),
            name: $entity->getName(),
            url: $entity->getUrl(),
            active: $entity->isActive(),
        );
    }
}
