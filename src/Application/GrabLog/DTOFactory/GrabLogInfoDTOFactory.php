<?php

declare(strict_types=1);

namespace App\Application\GrabLog\DTOFactory;

use App\Application\GrabLog\DTO\GrabLogInfoDTO;
use App\Domain\GrabLog\Entity\GrabLog;

final readonly class GrabLogInfoDTOFactory
{
    public function fromEntity(
        GrabLog $entity,
    ): GrabLogInfoDTO {
        return new GrabLogInfoDTO(
            id: $entity->getId(),
            source: $entity->getSource()?->getName(),
            description: $entity->getDescription(),
            counterCompanyCreated: $entity->getCounterCompanyCreated(),
            counterVacancyCreated: $entity->getCounterVacancyCreated(),
            counterVacancyUpdated: $entity->getCounterVacancyUpdated(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
