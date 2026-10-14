<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\DTOFactory;

use App\VacancyGrabber\Application\Source\DTO\SourceSummaryDTO;
use App\VacancyGrabber\Domain\Source\Entity\Source;

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
