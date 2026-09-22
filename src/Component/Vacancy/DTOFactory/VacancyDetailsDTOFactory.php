<?php

declare(strict_types=1);

namespace App\Component\Vacancy\DTOFactory;

use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\Entity\Vacancy;

final readonly class VacancyDetailsDTOFactory
{
    public function fromEntity(
        Vacancy $entity,
    ): VacancyDetailsDTO {
        return new VacancyDetailsDTO(
            id: $entity->getId(),
            //            name: $entity->getName(),
            //            url: $entity->getUrl(),
            //            active: $entity->isActive(),
        );
    }
}
