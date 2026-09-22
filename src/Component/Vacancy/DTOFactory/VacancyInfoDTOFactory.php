<?php

declare(strict_types=1);

namespace App\Component\Vacancy\DTOFactory;

use App\Component\Vacancy\DTO\VacancyInfoDTO;
use App\Component\Vacancy\Entity\Vacancy;

final readonly class VacancyInfoDTOFactory
{
    public function fromEntity(
        Vacancy $entity,
    ): VacancyInfoDTO {
        return new VacancyInfoDTO(
            id: $entity->getId(),
            //            name: $entity->getName(),
            //            url: $entity->getUrl(),
            //            active: $entity->isActive(),
        );
    }
}
