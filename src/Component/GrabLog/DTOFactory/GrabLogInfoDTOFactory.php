<?php

declare(strict_types=1);

namespace App\Component\GrabLog\DTOFactory;

use App\Component\GrabLog\DTO\GrabLogInfoDTO;
use App\Component\GrabLog\Entity\GrabLog;

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
            createdAt: $entity->getCreatedAt(),
        );
    }
}
