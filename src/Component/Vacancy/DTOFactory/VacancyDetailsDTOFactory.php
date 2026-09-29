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
            title: $entity->getTitle(),
            description: $entity->getDescription(),
            externalId: $entity->getExternalId(),
            url: $entity->getUrl(),
            salary: $entity->getSalary(),
            sourceId: $entity->getSource()?->getId(),
            sourceName: $entity->getSource()?->getName(),
            companyId: $entity->getCompany()?->getId(),
            companyName: $entity->getCompany()?->getName(),
            status: $entity->getStatus(),
            // notes: [],
            viewed: $entity->isViewed(),
            resumeSentAt: $entity->getResumeSentAt(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
