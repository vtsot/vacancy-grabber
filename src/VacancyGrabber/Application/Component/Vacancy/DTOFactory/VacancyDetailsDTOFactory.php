<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Vacancy\DTOFactory;

use App\VacancyGrabber\Application\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;

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
            deadLineAt: $entity->getDeadLineAt(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
