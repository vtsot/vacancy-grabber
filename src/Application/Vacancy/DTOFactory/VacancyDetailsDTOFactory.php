<?php

declare(strict_types=1);

namespace App\Application\Vacancy\DTOFactory;

use App\Application\Vacancy\DTO\VacancyDetailsDTO;
use App\Domain\Vacancy\Entity\Vacancy;

final readonly class VacancyDetailsDTOFactory
{
    public function fromEntity(
        Vacancy $entity,
    ): VacancyDetailsDTO {
        return new VacancyDetailsDTO(
            id: $entity->getId(),
            title: $entity->getTitle(),
            description: $entity->getDescription(),
            externalId: $entity->getExternalId()?->toString(),
            url: $entity->getUrl()?->toString(),
            salary: $entity->getSalary()?->toString(),
            sourceId: $entity->getSource()?->getId(),
            sourceName: $entity->getSource()?->getName(),
            companyId: $entity->getCompany()?->getId(),
            companyName: $entity->getCompany()?->getName(),
            status: $entity->getStatus(),
            // notes: [],
            viewed: $entity->isViewed(),
            resumeSentAt: $entity->getResumeSentAt(),
            deadLineAt: $entity->getDeadLineAt(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
