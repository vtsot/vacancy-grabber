<?php

declare(strict_types=1);

namespace App\Component\Vacancy\DTOFactory;

use App\Component\Vacancy\DTO\VacancyInfoDTO;
use App\Component\Vacancy\Entity\Vacancy;

final readonly class VacancyInfoDTOFactory
{
    public const string CONTEXT_NOTES = 'notes';

    public function fromEntity(
        Vacancy $entity,
        array $context = [],
    ): VacancyInfoDTO {
        return new VacancyInfoDTO(
            id: $entity->getId(),
            title: $entity->getTitle(),
            description: $entity->getDescription(),
            externalId: $entity->getExternalId(),
            url: $entity->getUrl(),
            salary: $entity->getSalary(),
            source: $entity->getSource()?->getName(),
            company: $entity->getCompany()?->getName(),
            status: $entity->getStatus(),
            notes: $context[self::CONTEXT_NOTES] ?? [],
            viewed: $entity->isViewed(),
            isResumeSent: $entity->getResumeSentAt() !== null,
            resumeSentAt: $entity->getResumeSentAt(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
