<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\DTOFactory;

use App\VacancyGrabber\Application\Source\DTO\SourceInfoDTO;
use App\VacancyGrabber\Domain\Source\Entity\Source;

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
            createdAt: $entity->getCreatedAt(),
        );
    }
}
