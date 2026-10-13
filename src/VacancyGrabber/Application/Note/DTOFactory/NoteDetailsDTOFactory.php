<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Note\DTOFactory;

use App\VacancyGrabber\Application\Note\DTO\NoteDetailsDTO;
use App\VacancyGrabber\Domain\Note\Entity\Note;

final readonly class NoteDetailsDTOFactory
{
    public function fromEntity(
        Note $entity,
    ): NoteDetailsDTO {
        return new NoteDetailsDTO(
            id: $entity->getId(),
            description: $entity->getDescription(),
            companyId: $entity->getVacancy()?->getCompany()?->getId(),
            companyName: $entity->getVacancy()?->getCompany()?->getName(),
            companyUrl: $entity->getVacancy()?->getCompany()?->getUrl(),
            notedAt: $entity->getNotedAt(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
