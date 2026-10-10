<?php

declare(strict_types=1);

namespace App\Component\Vacancy\DTOFactory;

use App\Component\Vacancy\DTO\VacancyInfoDTO;
use App\Component\Vacancy\Entity\Vacancy;

final readonly class VacancyInfoDTOFactory
{
    public const string CONTEXT_NOTES_COUNT = 'notes_count';

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
            sourceId: $entity->getSource()?->getId(),
            sourceName: $entity->getSource()?->getName(),
            companyId: $entity->getCompany()?->getId(),
            companyName: $entity->getCompany()?->getName(),
            status: $entity->getStatus(),
            notesCount: $context[self::CONTEXT_NOTES_COUNT] ?? 0,
            viewed: $entity->isViewed(),
            isResumeSent: $entity->getResumeSentAt() !== null,
            resumeSentAt: $entity->getResumeSentAt(),
            deadLineAt: $entity->getDeadLineAt(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
